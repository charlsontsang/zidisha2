<?php

namespace Zidisha\Loan;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Loan\Base\ForgivenLoanQuery as BaseForgivenLoanQuery;
use Zidisha\User\User;


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
            ->withColumn('SUM(usd_amount)', 'total')
            ->findOne();

        return Money::create($total, 'USD');
    }

    public function getForeignTotalAmount(Currency $currency)
    {
        $total = $this
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        // Todo currency
        return Money::create($total, $currency);
    }

    public function getTotalForgivenLendersForLoan(Loan $loan)
    {
        return $this
            ->filterByLoan($loan)
            ->filterByLenderId(Setting::get('site.adminId'), Criteria::NOT_EQUAL)
            ->count();
    }
} // ForgivenLoanQuery
