<?php

namespace Zidisha\Lender;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Lender\Base\InviteQuery as BaseInviteQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'lender_invites' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class InviteQuery extends BaseInviteQuery
{

    public function getAcceptedInviteesIds(Lender $lender)
    {
        return InviteQuery::create()
            ->select('invitee_id')
            ->filterByLender($lender)
            ->filterByInviteeId(null, Criteria::NOT_EQUAL)
            ->find();
    }
    
} // InviteQuery
