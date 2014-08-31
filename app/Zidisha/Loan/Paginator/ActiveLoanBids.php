<?php

namespace Zidisha\Loan\Paginator;

use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\ActiveBid;
use Zidisha\Loan\BidQuery;
use Zidisha\Repayment\RepaymentService;

class ActiveLoanBids extends AbstractBids
{
    /**
     * @var Money
     */
    protected $totalLentAmount;
    
    /**
     * @var Money
     */
    protected $totalRepaidAmount;
    
    /**
     * @var Money
     */
    protected $totalOutstandingAmount;

    protected function getResults()
    {
        $activeLoanBids = BidQuery::create()
            ->filterActiveLoanBids($this->lender)
            ->paginate($this->page, 10);
        
        $activeLoanIds = $activeLoanBids->toKeyValue('loanId', 'loanId');

        $repaidAmountByLoanId = TransactionQuery::create()
            ->getActiveLoansRepaidAmounts($this->lender->getId(), $activeLoanIds);

        $outstandingAmountByLoanId = BidQuery::create()
            ->getActiveLoansTotalOutstandingAmounts($this->lender, $activeLoanIds);
        
        $bids = [];

        /** @var RepaymentService $repaymentService */
        $repaymentService = \App::make('Zidisha\Repayment\RepaymentService');

        foreach ($activeLoanBids as $activeLoanBid) {
            $loanId = $activeLoanBid->getLoanId();
            
            if (!isset($bids[$loanId])) {
                $bids[$loanId] = new ActiveBid();
            }

            /** @var ActiveBid $activeBid */
            $activeBid = $bids[$loanId];
            $activeBid->addBid($activeLoanBid);

            if (isset($repaidAmountByLoanId[$loanId])) {
                $activeBid->setRepaidAmount(Money::create($repaidAmountByLoanId[$loanId], 'USD'));
            }

            if (isset($outstandingAmountByLoanId[$loanId])) {
                $activeBid->setOutstandingAmount(Money::create($outstandingAmountByLoanId[$loanId], 'USD'));
            }

            // TODO use caching
            $repaymentSchedule = $repaymentService->getRepaymentSchedule($activeLoanBid->getLoan());
            $activeBid->setLoanPaymentStatus($repaymentSchedule->getLoanPaymentStatus());
            
            $bids[$loanId] = $activeBid;
        }
        
        return [$bids, $activeLoanBids->getNbResults()];
    }

    public function getTotalLentAmount()
    {
        if ($this->totalLentAmount === null) {
            $this->totalLentAmount = BidQuery::create()
                ->getTotalActiveLoanBidsAmount($this->lender);
        }
        
        return $this->totalLentAmount;
    }
    
    public function getTotalRepaidAmount()
    {
        if ($this->totalRepaidAmount === null) {
            $this->totalRepaidAmount = TransactionQuery::create()
                ->getTotalActiveLoansRepaidAmount($this->lender->getId());
        }
        
        return $this->totalRepaidAmount;
    }
    
    public function getTotalOutstandingAmount()
    {
        if ($this->totalOutstandingAmount === null) {
            $this->totalOutstandingAmount = BidQuery::create()
                ->getTotalActiveLoanOutstandingAmount($this->lender);
        }
        
        return $this->totalOutstandingAmount;
    }
}
