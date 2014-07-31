<?php

namespace Zidisha\Balance;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Balance\Base\TransactionQuery as BaseTransactionQuery;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;


/**
 * Skeleton subclass for performing query and update operations on the 'transactions' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class TransactionQuery extends BaseTransactionQuery
{
    public function getTotalAmount()
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getTotalAmounts($userIds)
    {
        $amounts = [];
        $totals = $this
            ->filterByUserId($userIds)
            ->groupByUserId()
            ->select(array('user_id', 'total'))
            ->withColumn('SUM(amount)', 'total')
            ->find();

        foreach ($totals as $total) {
            $amounts[$total['user_id']] = Money::create($total['total']);
        }
        foreach ($userIds as $userId) {
            $amounts = $amounts + [$userId => Money::create(0)];
        }

        return $amounts;
    }

    public function filterLoanBids()
    {
        return $this->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID]);
    }
    
    public function filterDisbursement()
    {
        return $this->filterByType(Transaction::DISBURSEMENT);
    }
    
    public function filterServiceFee()
    {
        return $this->filterByType(Transaction::FEE);
    }

    public function filterFundUpload()
    {
        return $this->filterByType(Transaction::FUND_UPLOAD);
    }

    public function filterFundWithdraw()
    {
        return $this->filterByType(Transaction::FUND_WITHDRAW);
    }
    public function filterRepaidToLender()
    {
        return $this->filterByType(Transaction::LOAN_BACK_LENDER);
    }
    
    public function getForeignTotalAmount(Currency $currency)
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(amount * exchange_rate)', 'total')
            ->findOne();

        return Money::create($total ?: 0, $currency);
    }

    public function getTotalLentAmount($userId)
    {
        $total = $this
            ->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID], Criteria::IN)
            ->filterByUserId($userId)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD')->multiply(-1);
    }

    public function getTotalGroupMembersLentAmount($userIds)
    {
        $total = $this
            ->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID], Criteria::IN)
            ->filterByUserId($userIds , Criteria::IN)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD')->multiply(-1);
    }

    public function getTotalGroupMembersLentAmountThisMonth($userIds)
    {
        $startDate = date('01-m-Y'); // day one of the current month
        $total = $this
            ->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID], Criteria::IN)
            ->filterByUserId($userIds , Criteria::IN)
            ->filterByTransactionDate($startDate, Criteria::GREATER_EQUAL)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD')->multiply(-1);
    }

    public function getTotalGroupMembersLentAmountLastMonth($userIds)
    {
        $startDate = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y"))); // day one of the last month
        $endDate = date('01-m-Y'); // day one of the current month
        $total = $this
            ->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID], Criteria::IN)
            ->filterByUserId($userIds, Criteria::IN)
            ->where('transactions.transaction_date BETWEEN ? AND ?', array($startDate, $endDate))
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD')->multiply(-1);
    }

    public function getPrincipalOutstanding($userId)
    {
        $total = $this
            ->filterByType([Transaction::LOAN_BID, Transaction::LOAN_OUTBID, Transaction::LOAN_BACK_LENDER], Criteria::IN)
            ->filterByUserId($userId)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD')->multiply(-1);
    }
} // TransactionQuery
