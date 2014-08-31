<?php

namespace Zidisha\Loan\Paginator;

use Zidisha\Loan\BidQuery;

class FundraisingLoanBids extends AbstractBids
{
    protected $totalBidAmount;

    protected function getResults()
    {
        $bids = BidQuery::create()
            ->filterFundraisingLoanBids($this->lender, $this->page)
            ->paginate($this->page , 10);
        
        return [$bids, $bids->getNbResults()];
    }

    public function getTotalBidAmount()
    {
        if ($this->totalBidAmount === null) {
            $this->totalBidAmount  = BidQuery::create()
                ->getTotalFundraisingLoanBidAmount($this->lender);
        }
        
        return $this->totalBidAmount;
    }
}
