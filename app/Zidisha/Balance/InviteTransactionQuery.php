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
            ->find();
        if ($total->getData()) {
            $results = $total->toKeyValue('lenderId', 'total');

            if (count($lenderIds) > 1) {
                $creditArray = [];
                foreach ($results as $key=>$value) {
                    $creditArray[$key] = Money::create($value, 'USD');
                }
                foreach ($lenderIds as $lenderId) {
                    if (!isset($creditArray[$lenderId])) {
                        $creditArray[$lenderId] = Money::create(0, 'USD');
                    }
                }
                return $creditArray;
            } else {
                return Money::create($results[$lenderIds], 'USD');
            }
        } else {
            return Money::create(0, 'USD');
        }
    }
} // InviteTransactionQuery
