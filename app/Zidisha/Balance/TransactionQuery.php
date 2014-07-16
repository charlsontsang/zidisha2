<?php

namespace Zidisha\Balance;

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
    
    public function getNativeTotalAmount(Currency $currency)
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(amount * exchangeRate)', 'total')
            ->findOne();

        return Money::create($total, $currency);
    }
} // TransactionQuery
