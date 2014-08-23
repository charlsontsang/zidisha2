<?php

use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Calculator\BidsCalculator;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;

class BorrowerLoanController extends BaseController
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
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
            $installmentCalculator = new InstallmentCalculator($loan);
            $data['calculator'] = $installmentCalculator;
            $data['lenderInterestRate'] = $lenderInterestRate;
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

        if (!empty(Input::get('acceptBidsNote'))) {
            $loan->setAcceptBidsNote(Input::get('acceptBidsNote'));
            $loan->save();
        }

        $this->loanService->acceptBids($loan);

        \Flash::success('You have accepted the loan bids successfully.');
        return Redirect::action('BorrowerLoanController@getLoanInformation', ['loanId' => $loanId] );
    }
} 