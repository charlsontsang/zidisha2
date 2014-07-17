<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
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
} // InstallmentQuery
