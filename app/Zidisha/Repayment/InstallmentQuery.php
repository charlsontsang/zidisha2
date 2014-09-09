<?php

namespace Zidisha\Repayment;

use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\Base\Borrower;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Base\InstallmentQuery as BaseInstallmentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'installments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class InstallmentQuery extends BaseInstallmentQuery
{

    public function getForeignAmounts(Currency $currency)
    {
        $result = $this
            ->select(['totalAmount', 'paidAmount'])
            ->withColumn('SUM(amount)', 'totalAmount')
            ->withColumn('SUM(paid_amount)', 'paidAmount')
            ->findOne();
        
        return [
            'totalAmount' => Money::create($result['totalAmount'], $currency),
            'paidAmount'  => Money::create($result['paidAmount'], $currency),
        ];
    }

    public function getPaidAmount(Loan $loan)
    {
        return $this
            ->filterByLoan($loan)
            ->select('paidAmount')
            ->withColumn('SUM(paid_amount)', 'paidAmount')
            ->findOne();
    }

    /**
     * @param Loan $loan
     * @return Money
     */
    public function getLastInstallmentAmount(Loan $loan)
    {
        $amount = $this
            ->filterByLoan($loan)
            ->orderByDueDate('desc')
            ->select('paidAmount')
            ->withColumn('paid_amount', 'paidAmount')
            ->findOne();

        return Money::create($amount ?: 0, $loan->getCurrencyCode());
    }

    public function getDueInstallment(Loan $loan)
    {
        return $this
            ->filterByLoan($loan)
            ->filterByAmount(0, Criteria::GREATER_THAN)
            ->where('(Installment.PaidAmount IS NULL OR Installment.PaidAmount < Installment.Amount)')
            ->where('Installment.DueDate < ?', Carbon::now())
            ->orderByDueDate('ASC')
            ->findOne();
    }
} // InstallmentQuery
