<?php

namespace Zidisha\Balance;

use Zidisha\Balance\Base\InviteTransactionQuery as BaseInviteTransactionQuery;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;


/**
 * Skeleton subclass for performing query and update operations on the 'lender_invite_transactions' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class InviteTransactionQuery extends BaseInviteTransactionQuery
{

    public function getTotalInviteCreditAmount(Lender $lender)
    {
        $total = $this
            ->filterByLender($lender)
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->findOne();

        return Money::valueOf($total, Currency::valueOf('USD'));

    }
} // InviteTransactionQuery
