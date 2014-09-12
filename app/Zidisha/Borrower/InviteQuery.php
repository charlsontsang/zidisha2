<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\InviteQuery as BaseInviteQuery;
use Zidisha\Vendor\PropelDB;


/**
 * Skeleton subclass for performing query and update operations on the 'borrower_invites' table.
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
    public function getInvitee($inviteeId)
    {
        return $this
            ->filterByInviteeId($inviteeId)
            ->findOne();
    }

    public function getInvitesWithoutPaymentCount($borrower)
    {
        $q = 'SELECT COUNT(*) FROM borrower_invites i
              WHERE i.borrower_id = :borrowerId
                AND (i.invitee_id IS NULL
                     OR i.invitee_id = 0
                     OR NOT EXISTS (SELECT * FROM installment_payments r
                                    WHERE r.borrower_id = i.invitee_id))';

        $invitesCount = PropelDB::fetchNumber($q, [
            'borrowerId' => $borrower->getId(),
        ]);

        return $invitesCount;
    }
} // InviteQuery
