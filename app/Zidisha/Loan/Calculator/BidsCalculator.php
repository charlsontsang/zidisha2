<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;

class BidsCalculator {


    public function getAcceptedBids($bids, Money $loanAmount)
    {
        $zero = Money::create(0, 'USD');
        $totalBidAmount = $zero;
        $acceptedBids = [];

        /** @var Bid $bid */
        foreach ($bids as $bid) {
            $bidAmount = $bid->getBidAmount();
            $missingAmount = $loanAmount->subtract($totalBidAmount)->max($zero)->round(3);
            $totalBidAmount = $totalBidAmount->add($bidAmount);
            $acceptedAmount = $missingAmount->min($bidAmount);

            $acceptedBids[$bid->getId()] = compact('bid', 'acceptedAmount');
        }

        // Sort by bid date, this changes the order of the transactions (amounts are the same)
        uasort(
            $acceptedBids,
            function ($b1, $b2) {
                return $b1['bid']->getBidAt() <= $b2['bid']->getBidAt();
            }
        );

        return $acceptedBids;
    }

    public function getChangedBids($oldAcceptedBids, $newAcceptedBids)
    {
        $changedBids = [];

        foreach ($newAcceptedBids as $bidId => $acceptedBid) {
            /** @var Money $acceptedAmount */
            $acceptedAmount = $acceptedBid['acceptedAmount'];
            /** @var Bid $bid */
            $bid = $acceptedBid['bid'];
            if (isset($oldAcceptedBids[$bidId])) {
                /** @var Money $oldAcceptedAmount */
                $oldAcceptedAmount = $oldAcceptedBids[$bidId]['acceptedAmount'];
                if ($oldAcceptedAmount->greaterThan($acceptedAmount)) {
                    $changedBids[$bidId] = [
                        'bid'            => $bid,
                        'acceptedAmount' => $acceptedAmount,
                        'type'           => 'out_bid',
                        'changedAmount'  => $oldAcceptedAmount->subtract($acceptedAmount),
                    ];
                } else {
                    if ($oldAcceptedAmount->lessThan($acceptedAmount)) {
                        $changedBids[$bidId] = [
                            'bid'            => $bid,
                            'acceptedAmount' => $acceptedAmount,
                            'type'           => 'update_bid',
                            'changedAmount'  => $acceptedAmount->subtract($oldAcceptedAmount),
                        ];
                    }
                }
            } elseif ($acceptedAmount->greaterThan(Money::create(0))) {
                $changedBids[$bidId] = [
                    'bid'            => $bid,
                    'acceptedAmount' => $acceptedAmount,
                    'type'           => 'place_bid',
                    'changedAmount'  => $acceptedAmount,
                ];
            }
        }

        return $changedBids;
    }
}
