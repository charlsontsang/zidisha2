<?php

use Zidisha\Admin\Form\RescheduleLoanForm;
use Zidisha\Borrower\Borrower;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Calculator\BidsCalculator;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Calculator\RescheduleCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\RepaymentService;

class BorrowerLoanController extends BaseController
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;
    
    /**
     * @var Zidisha\Repayment\RepaymentService
     */
    private $repaymentService;

    public function __construct(LoanService $loanService, RepaymentService $repaymentService)
    {
        $this->loanService = $loanService;
        $this->repaymentService = $repaymentService;
    }

    public function getLoanInformation($loanId)
    {
        $borrower = \Auth::user()->getBorrower();

        $loan = LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan || $borrower != $loan->getBorrower()) {
            App::abort('404');
        }

        $data = compact('loan', 'borrower');

        if ($loan->isOpen()) {
            $bids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();

            $data['bids'] = $bids;
            $bidsCalculator = new BidsCalculator();
            $acceptedBids = $bidsCalculator->getAcceptedBids($bids, $loan->getUsdAmount());
            $lenderInterestRate = $bidsCalculator->getLenderInterestRate($acceptedBids, $loan->getUsdAmount());
            $loan->setLenderInterestRate($lenderInterestRate);
            $loan->setDisbursedAt(new \DateTime());
            
            $installmentCalculator = new InstallmentCalculator($loan);
            $installments = $installmentCalculator->generateLoanInstallments();
            unset($installments[0]);
            
            $data['calculator'] = $installmentCalculator;
            $data['installments'] = $installments;
        }

        return View::make('borrower.loan.loan-information' , $data);
    }

    public function postAcceptBids($loanId)
    {
        $borrower = \Auth::user()->getBorrower();

        $loan = LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan || $borrower != $loan->getBorrower()) {
            App::abort('404');
        }

        if (!$loan->isOpen()) {
            App::abort('404', 'Loan is not open');
        }

        if (!Input::get('acceptBidsNote')) {
            $loan->setAcceptBidsNote(Input::get('acceptBidsNote'));
            $loan->save();
        }

        $this->loanService->acceptBids($loan);

        \Flash::success('You have accepted the loan bids successfully.');
        return Redirect::action('BorrowerLoanController@getLoanInformation', ['loanId' => $loanId] );
    }

    public function getRescheduleLoan()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        $loan = $borrower->getActiveLoan();

        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);

        $rescheduleCalculator = new RescheduleCalculator($loan, $repaymentSchedule);
        $minInstallmentAmount = $rescheduleCalculator->minInstallmentAmount();
        $this->validateReschedule($loan);

        $form = new RescheduleLoanForm($rescheduleCalculator);

        return View::make(
            'borrower.loan.reschedule-loan',
            compact('borrower', 'loan', 'repaymentSchedule', 'form', 'minInstallmentAmount')
        );
    }

    public function postRescheduleLoan()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        $loan = $borrower->getActiveLoan();

        $this->validateReschedule($loan);

        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        $rescheduleCalculator = new RescheduleCalculator($loan, $repaymentSchedule);
        $form = new RescheduleLoanForm($rescheduleCalculator);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            \Flash::success('Successfully rescheduled your loan.');
            return Redirect::route('borrower:loan-information', $loan->getId());
        }
        
        \Flash::error('Invalid input values.');
        
        return Redirect::route('borrower:reschedule-loan')->withForm($form);
    }

    protected function validateReschedule(Loan $loan)
    {
        // TODO check if reschedule is allowed
        if (!$loan || !$loan->isActive()) {
            App::abort('404');
        }
    }
} 