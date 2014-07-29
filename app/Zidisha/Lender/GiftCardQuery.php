<?php

namespace Zidisha\Lender;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Lender\Base\GiftCardQuery as BaseGiftCardQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'gift_cards' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class GiftCardQuery extends BaseGiftCardQuery
{

    public function getRedeemedGiftCardsRecipientsIds(Lender $lender)
    {
        return GiftCardQuery::create()
            ->select('recipient_id')
            ->filterByLender($lender)
            ->filterByRecipientId(null, Criteria::NOT_EQUAL)
            ->find();
    }

} // GiftCardQuery
