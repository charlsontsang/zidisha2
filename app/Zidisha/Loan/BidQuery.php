<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\BidQuery as BaseBidQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'loan_bids' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class BidQuery extends BaseBidQuery
{
    /**
     * @return Money
     */
    public function getTotalBidAmount()
    {
        $total = $this->select(array('total'))
            ->withColumn('SUM(bid_amount)', 'total')
            ->findOne();

        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getOrderedBids(Loan $loan)
    {
        return $this
            ->filterByLoan($loan)
            ->orderByInterestRate()
            ->orderByBidDate();
    }

} // BidQuery
