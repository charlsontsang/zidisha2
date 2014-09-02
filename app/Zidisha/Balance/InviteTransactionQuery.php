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

    public function getTotalInviteCreditAmount($lenderIds)
    {
        $total = $this
            ->filterByLenderId($lenderIds)
            ->select(array('total', 'lenderId'))
            ->withColumn('SUM(amount)', 'total')
            ->withColumn('lender_id', 'lenderId')
            ->groupByLenderId()
            ->findOne();
        $results = $total->toKeyValue('userId', 'total');

        if (count($lenderIds) > 1) {
            $creditArray = [];
            foreach ($results as $key=>$value) {
                $creditArray[$key] = Money::create($value, 'USD');
            }
            return $creditArray;
        } else {
            return Money::create($results[$lenderIds], 'USD');
        }
    }
} // InviteTransactionQuery
