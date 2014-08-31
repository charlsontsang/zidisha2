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
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(bid_amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
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
            ->where("Bid.lender_id NOT IN (SELECT lender_id from forgiveness_loan_shares where loan_id = $loanId)");
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

    public function filterFundraisingLoanBids(Lender $lender)
    {
        return $this
            ->filterByLender($lender)
            ->filterByActive(false)
            ->useLoanQuery()
                ->filterFundRaising()
            ->endUse();
    }

    public function getTotalFundraisingLoanBidAmount(Lender $lender)
    {
        return $this
            ->filterFundraisingLoanBids($lender)
            ->getTotalBidAmount();
    }

    public function getActiveLoanBids(Lender $lender, $page2)
    {
        return $this
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterActive()
            ->endUse()
            ->paginate($page2, 2);
    }

    public function getTotalActiveLoanBidsAmount(Lender $lender)
    {
        $total = $this
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
            ->filterActive()
            ->endUse()
            ->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getCompletedLoansBids(Lender $lender, $page3)
    {
        return $this
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
            ->filterEnded()
            ->endUse()
            ->paginate($page3, 2);
    }

    public function getTotalCompletedLoansBidsAmount(Lender $lender)
    {
        $total = $this
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
            ->filterEnded()
            ->endUse()
            ->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getTotalOutstandingAmount(Lender $lender)
    {
        $total = $this
            ->filterByActive(true)
            ->filterByLender($lender)
            ->useLoanQuery()
            ->filterByStatus([Loan::FUNDED, Loan::ACTIVE])
            ->filterNotForgivenByLender($lender)
            ->endUse()
            ->withColumn('SUM(accepted_amount * (100 - paid_percentage)/100)', 'total')
            ->select('total')
            ->findOne();
        return Money::valueOf($total, Currency::valueOf('USD'));
    }

    public function getActiveLoansTotalOutstandingAmounts(Lender $lender, $activeLoansIds)
    {
        return $this
            ->filterByActive(true)
            ->filterByLender($lender)
            ->useLoanQuery()
                ->filterByStatus([Loan::FUNDED, Loan::ACTIVE])
                ->filterNotForgivenByLender($lender)
                ->filterById($activeLoansIds, Criteria::IN)
            ->endUse()
            ->select('total', 'loan_id')
            ->withColumn('SUM(accepted_amount * (100 - paid_percentage)/100)', 'total')
            ->withColumn('loan_id', 'loan_id')
            ->groupByLoanId()
            ->find();
    }

    public function getTotalActiveLoansTotalOutstandingAmount(Lender $lender)
    {
        $total = $this
            ->filterByActive(true)
            ->filterByLender($lender)
            ->useLoanQuery()
                ->filterByStatus([Loan::FUNDED, Loan::ACTIVE])
                ->filterNotForgivenByLender($lender)
            ->endUse()
            ->select('total')
            ->withColumn('SUM(accepted_amount * (100 - paid_percentage)/100)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }
} // BidQuery
