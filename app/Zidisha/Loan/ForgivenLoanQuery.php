<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Loan\Base\ForgivenLoanQuery as BaseForgivenLoanQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'forgiven_loans' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ForgivenLoanQuery extends BaseForgivenLoanQuery
{
    public function getTotalAmount()
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getForeignTotalAmount(Currency $currency)
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(native_amount)', 'total')
            ->findOne();

        // Todo currency
        return Money::create($total, $currency);
    }
} // ForgivenLoanQuery
