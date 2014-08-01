<?php

namespace Zidisha\Repayment;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Repayment\Base\BorrowerRefundQuery as BaseBorrowerRefundQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'borrower_refunds' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class BorrowerRefundQuery extends BaseBorrowerRefundQuery
{

    public function updateRefundToTrue($refundsIds)
    {
        return $this->filterById($refundsIds, Criteria::IN)
            ->update(array('Refunded' => true));
    }
} // BorrowerRefundQuery
