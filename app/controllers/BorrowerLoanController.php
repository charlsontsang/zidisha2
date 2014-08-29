<?php

use Zidisha\Admin\Form\RescheduleLoanForm;
use Zidisha\Borrower\Borrower;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Calculator\BidsCalculator;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\RepaymentSchedule;
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

        $this->validateReschedule($loan);

        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        $form = new RescheduleLoanForm($loan, $repaymentSchedule);

        return View::make(
            'borrower.loan.reschedule-loan',
            compact('borrower', 'loan', 'repaymentSchedule', 'form')
        );
    }

    public function postRescheduleLoan()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        $loan = $borrower->getActiveLoan();

        $this->validateReschedule($loan);

        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        $form = new RescheduleLoanForm($loan, $repaymentSchedule);
        $form->handleRequest(Request::instance());

        \Session::forget('reschedule');
        
        if ($form->isValid()) {
            \Session::put('reschedule', $form->getData());

            return Redirect::route('borrower:reschedule-loan-confirmation');
        }
        
        \Flash::error('Invalid input values.');
        
        return Redirect::route('borrower:reschedule-loan')->withForm($form);
    }

    public function getRescheduleLoanConfirmation()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        $loan = $borrower->getActiveLoan();

        $this->validateReschedule($loan);
        $this->validateRescheduleConfirmation();

        $repaymentSchedule = $this->loanService->rescheduleLoan(
            $loan,
            Session::get('reschedule'),
            true
        );
        
        \Session::put('rescheduleDetails', $this->extractRescheduleDetails($repaymentSchedule));

        return View::make(
            'borrower.loan.reschedule-loan-confirmation',
            compact('borrower', 'loan', 'repaymentSchedule')
        );
    }

    public function postRescheduleLoanConfirmation()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        $loan = $borrower->getActiveLoan();

        $this->validateReschedule($loan);
        $this->validateRescheduleConfirmation();

        $rescheduleDetailsSession = \Session::get('rescheduleDetails');
        \Session::forget('rescheduleDetails');

        $repaymentSchedule = $this->loanService->rescheduleLoan(
            $loan,
            \Session::get('reschedule'),
            true
        );
        
        $rescheduleDetails = $this->extractRescheduleDetails($repaymentSchedule);

        if ($rescheduleDetails != $rescheduleDetailsSession) {
            Flash::error('Session is expired.');
            return Redirect::route('borrower:reschedule-loan');
        }
        
        $this->loanService->rescheduleLoan($loan, \Session::get('reschedule'));

        \Session::forget('reschedule');

        Flash::success('Successfully rescheduled loan');
        return Redirect::route('borrower:loan-information', $loan->getId());
    }

    protected function validateReschedule(Loan $loan)
    {
        // TODO check if reschedule is allowed
        if (!$loan || !$loan->isActive()) {
            App::abort('404');
        }
    }
    
    protected function validateRescheduleConfirmation()
    {        
        if (!\Session::has('reschedule.installmentAmount') || !\Session::has('reschedule.reason')) {
            App::abort('404');
        }
    }

    protected function extractRescheduleDetails(RepaymentSchedule $repaymentSchedule)
    {
        return [
            'period' => $repaymentSchedule->getPeriod(),
            'totalInterest' => $repaymentSchedule->getTotalInterest()->getAmount(),
            'totalAmountDue' => $repaymentSchedule->getTotalAmountDue()->getAmount(),
        ];
    }
} 