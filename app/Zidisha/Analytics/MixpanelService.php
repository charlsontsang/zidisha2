<?php
namespace Zidisha\Analytics;

use Zidisha\Lender\Invite;
use Zidisha\Lender\InviteVisit;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\User\User;
use Zidisha\Vendor\DummyMixpanel;
use Zidisha\Vendor\Mixpanel;

class MixpanelService
{
    /**
     * @var \Zidisha\Vendor\Mixpanel
     */
    private $mixpanel;

    public function __construct(Mixpanel $mixpanel, DummyMixpanel $dummyMixpanel)
    {
        if (\Config::get('services.mixpanel.enabled')) {
            $this->mixpanel = $mixpanel;
        } else {
            $this->mixpanel = $dummyMixpanel;
        }
    }

    public function identify(User $user)
    {
        $this->mixpanel->identify(
            $user->getId(),
            [
                'username'  => $user->getUsername(),
                'userlevel' => $user->getRole(),
                'email'     => $user->getEmail(),
            ]
        );
    }

    public function trackLenderJoined(Lender $lender)
    {
        $this->mixpanel->alias($lender->getId());
        $this->mixpanel->track('Signed up', array(
            'username'  => $lender->getUser()->getUsername(),
            'userlevel' => 'lender',
            'email'     => $lender->getUser()->getEmail(),
        ));
    }

    public function trackPlacedBid(Bid $bid)
    {
        $amount = $bid->getBidAmount()->getAmount();
        $interest = $bid->getInterestRate();
        $bidId = $bid->getId();

        $this->mixpanel->track(
            'Purchased an Item',
            array(
                'amount' => $amount,
                'type' => 'Loan Bid',
                'interest' => $interest,
                'id' => $bidId,
                'lender invite credit' => $bid->getIsLenderInviteCredit(),
            )
        );
    }

    public function trackInvitePage(Lender $lender, InviteVisit $inviteVisit, $shareType)
    {
        if ($inviteVisit->getLenderInviteId() != null) {
            $this->mixpanel->track(
                'Invite page viewed',
                array(
                    'share_type' => $shareType,
                    'email' => $inviteVisit->getInvite()->getEmail(),
                    'lender_id' => $lender->getId()
                )
            );
        } else {
            $this->mixpanel->track(
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
        $this->mixpanel->track(
            'Invite accepted',
            array(
                'username' => $invite->getInvitee()->getUser()->getUsername(),
                'email' => $invite->getEmail(),
                'lender_id' => $invite->getLenderId()
            )
        );
    }

    public function trackLoggedIn()
    {
        $this->mixpanel->track('Logged in');
    }

} 