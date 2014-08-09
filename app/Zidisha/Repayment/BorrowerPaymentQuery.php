<?php

namespace Zidisha\Repayment;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\Borrower;
use Zidisha\Repayment\Base\BorrowerPaymentQuery as BaseBorrowerPaymentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'borrower_payments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class BorrowerPaymentQuery extends BaseBorrowerPaymentQuery
{

    public function updateStatusToDeleted($paymentIds)
    {
        return $this->filterById($paymentIds, Criteria::IN)
            ->update(array('Status' => Borrower::PAYMENT_DELETED));
    }
} // BorrowerPaymentQuery
