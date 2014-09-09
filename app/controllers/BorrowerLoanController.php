<?php

use Zidisha\Admin\Form\RescheduleLoanForm;
use Zidisha\Borrower\Borrower;
use Zidisha\Comment\LoanFeedbackCommentService;
use Zidisha\Lender\LenderQuery;
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

    private $loanService;
    private $repaymentService;
    private $loanFeedbackCommentService;

    public function __construct(LoanService $loanService, RepaymentService $repaymentService, LoanFeedbackCommentService $loanFeedbackCommentService)
    {
        $this->loanService = $loanService;
        $this->repaymentService = $repaymentService;
        $this->loanFeedbackCommentService = $loanFeedbackCommentService;
    }

    public function getLoan($loanId = null)
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        
        if (!$loanId) {
            $loan = $borrower->getActiveLoan();
            if (!$loan) {
                // TODO render no active loan template, show previous loans
                return View::make('borrower.loan.loan-no-loans');
            }
        } else {
            $loan = LoanQuery::create()
                ->findOneById($loanId);

            if (!$loan || $borrower != $loan->getBorrower()) {
                App::abort('404');
            }
        }
        
        $loans = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderById()
            ->find();

        $data = compact('loan', 'loans', 'borrower');
        $template = 'borrower.loan.loan-base';

        if ($loan->isActive() || $loan->isDefaulted() || $loan->isRepaid()) {
            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);

            $data['repaymentSchedule'] = $repaymentSchedule;
        }

        if ($loan->isOpen() || $loan->isFunded() ) {
            $data['lenders'] = LenderQuery::create()->findBidOnLoan($loan);
            
            $bids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();

            $data['bids'] = $bids;
            $bidsCalculator = new BidsCalculator();
            $acceptedBids = $bidsCalculator->getAcceptedBids($bids, $loan->getUsdAmount());
            $lenderInterestRate = $bidsCalculator->getLenderInterestRate($acceptedBids, $loan->getUsdAmount());
            $loan
                ->setLenderInterestRate($lenderInterestRate)
                ->setDisbursedAt(new \DateTime());
            
            $installmentCalculator = new InstallmentCalculator($loan);
            $repaymentSchedule = RepaymentSchedule::createFromInstallments($loan, $installmentCalculator->generateLoanInstallments());
            
            $data['installmentCalculator'] = $installmentCalculator;
            $data['repaymentSchedule'] = $repaymentSchedule;

            $template = $loan->isOpen() ? 'borrower.loan.loan-open' : 'borrower.loan.loan-funded';
        } elseif ($loan->isActive()) {

            $template = 'borrower.loan.loan-active';
        } elseif ($loan->isRepaid() || $loan->isDefaulted()) {
            // TODO, same todo as in LoanController
            $displayFeedbackComments = ($loan->getStatus() == Loan::DEFAULTED || $loan->getStatus() == Loan::REPAID);

            $canPostFeedback = false;
            $canReplyFeedback = false;
            if ($displayFeedbackComments && Auth::check()) {
                $user = Auth::user();

                if ($user == $loan->getBorrower()->getUser()) {
                    $canReplyFeedback = true;
                }
            }

            //TODO
            $feedbackCommentPage = Input::get('feedbackPage', 1);

            $loanFeedbackComments = $this->loanFeedbackCommentService->getPaginatedComments($loan, $feedbackCommentPage, 10);

            $data += compact('canPostFeedback', 'canReplyFeedback', 'loanFeedbackComments');

            if ($loan->isRepaid()) {
                $template = 'borrower.loan.loan-repaid';
            } else {
                $template = 'borrower.loan.loan-defaulted';
            }
        } elseif ($loan->isExpired()) {
            $template = 'borrower.loan.loan-expired';
        } elseif ($loan->isCanceled()) {
            $template = 'borrower.loan.loan-canceled';
        }
        
        return View::make($template , $data);
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
        
        return Redirect::action('BorrowerLoanController@getLoan', ['loanId' => $loanId] );
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
        \Session::forget('rescheduleDetails');

        Flash::success('Successfully rescheduled loan');
        return Redirect::route('borrower:loan', $loan->getId());
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