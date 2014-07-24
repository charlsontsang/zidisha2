<?php

namespace Zidisha\Loan;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Base\BidQuery as BaseBidQuery;
use Zidisha\Vendor\PropelDB;


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

    public function getTotalAcceptedAmount()
    {
        $total = $this->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();

        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getOrderedBids(Loan $loan)
    {
        return $this
            ->filterByLoan($loan)
            ->orderByInterestRate()
            ->orderByBidAt();
    }

    public function filterBidsToRepay(Loan $loan)
    {
        $loanId = $loan->getId();
        
        return $this
            ->filterByLoan($loan)
            ->filterByActive(true)
            ->where("Bid.lender_id NOT IN (SELECT lender_id from forgiven_loans where loan_id = $loanId)");
    }

    public function getTotalActiveBidAmount(Lender $lender)
    {
        $total =  $this
            ->filterByLender($lender)
            ->filterByActive(true)
            ->select(array('total'))
            ->withColumn('SUM(bid_amount)', 'total')
            ->findOne();

        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getTotalOpenLoanBidAmount(Lender $lender)
    {
        $sql = 'SELECT SUM(t.amount)
                FROM transactions AS t JOIN loans AS l ON t.loan_id = l.id
                WHERE t.user_id = :userId
                  AND t.type IN (:loanBid, :loanOutbid)
                  AND l.status = :status';

        return PropelDB::fetchNumber($sql, [
                'userId' => $lender->getId(),
                'loanBid' => Transaction::LOAN_BID,
                'loanOutbid' => Transaction::LOAN_OUTBID,
                'status' => Loan::OPEN
            ]);

    }

    public function getTotalLoans(Lender $lender, $page, $page2, $page3)
    {
        $activeBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByActive(true)
            ->paginate($page , 10);
        $totalBidAmount = BidQuery::create()
            ->getTotalActiveBidAmount($lender);

        $activeLoansBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
            ->filterActive()
            ->endUse()
            ->paginate($page2, 10);

        $total = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
            ->filterActive()
            ->endUse()
            ->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        $totalActiveLoansBidsAmount =  Money::valueOf($total, Currency::valueOf('USD'));


        $completedLoansBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterEnded()
            ->endUse()
            ->paginate($page3, 1);
        $total = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterEnded()
            ->endUse()
            ->select(array('total', 'id'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        $totalCompletedLoansBidsAmount =  Money::valueOf($total, Currency::valueOf('USD'));

        return [
            'activeBids' => $activeBids,
            'totalBidAmount' => $totalBidAmount,
            'activeLoansBids' => $activeLoansBids,
            'totalActiveLoansBidsAmount' => $totalActiveLoansBidsAmount,
            'completedLoansBids' => $completedLoansBids,
            'totalCompletedLoansBidsAmount' => $totalCompletedLoansBidsAmount,
        ];
    }

} // BidQuery
