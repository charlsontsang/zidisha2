<?php
namespace Zidisha\Analytics;


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
} 