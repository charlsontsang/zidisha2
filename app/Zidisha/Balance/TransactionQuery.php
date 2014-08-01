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

    public function getActiveLoansRepaidAmounts($userId, $activeLoansIds)
    {
        return $this
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->select('totals', 'loan_id')
            ->withColumn('SUM(amount)', 'totals')
            ->withColumn('loan_id', 'loan_id')
            ->filterByLoanId($activeLoansIds, Criteria::IN)
            ->groupByLoanId()
            ->find();
    }

    public function getTotalActiveLoansRepaidAmount($userId)
    {
        $total = $this
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->useLoanQuery()
                ->filterActive()
            ->endUse()
            ->select('totals')
            ->withColumn('SUM(Transaction.amount)', 'totals')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getCompletedLoansRepaidAmounts($userId, $completedLoansIds)
    {
        return $this
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->select('totals', 'loan_id')
            ->withColumn('SUM(amount)', 'totals')
            ->withColumn('loan_id', 'loan_id')
            ->filterByLoanId($completedLoansIds, Criteria::IN)
            ->groupByLoanId()
            ->find();
    }

    public function getTotalCompletedLoansRepaidAmount($userId)
    {
        $total = $this
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->useLoanQuery()
                ->filterEnded()
            ->endUse()
            ->select('totals')
            ->withColumn('SUM(Transaction.amount)', 'totals')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getTotalFundsUpload($userId)
    {
        $total =  $this
            ->filterByUserId($userId)
            ->filterByType(Transaction::FUND_UPLOAD)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getCurrentBalance($userId)
    {
        $total = $this
            ->filterByUserId($userId)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }
} // TransactionQuery
