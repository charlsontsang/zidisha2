<?php
namespace Zidisha\Analytics;

use Zidisha\Lender\Invite;
use Zidisha\Lender\InviteVisit;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Vendor\Mixpanel;

class MixpanelService
{
    public function trackPlacedBid(Bid $bid)
    {
        $amount = $bid->getBidAmount()->getAmount();
        $interest = $bid->getInterestRate();
        $bidId = $bid->getId();

        Mixpanel::track(
            'Purchased an Item',
            array(
                'amount' => $amount,
                'type' => 'Loan Bid',
                'interest' => $interest,
                'id' => $bidId,
                // TODO: Lender invite
                'lender invite credit' => false,
            )
        );
    }

    public function trackInvitePage(Lender $lender, InviteVisit $inviteVisit, $shareType)
    {
        if ($inviteVisit->getLenderInviteId() != null) {
            Mixpanel::track(
                'Invite page viewed',
                array(
                    'share_type' => $shareType,
                    'email' => $inviteVisit->getInvite()->getEmail(),
                    'lender_id' => $lender->getId()
                )
            );
        } else {
            Mixpanel::track(
                'Invite page viewed',
                array(
                    'share_type' => $inviteVisit->getHumanShareType(),
                    'lender_id' => $lender->getId(),
                )
            );
        }
    }

    public function trackInviteAccept(Invite $invite)
    {
        Mixpanel::track(
            'Invite accepted',
            array(
                'username' => $invite->getInvitee()->getUser()->getUsername(),
                'email' => $invite->getEmail(),
                'lender_id' => $invite->getLenderId()
            )
        );
    }

} 