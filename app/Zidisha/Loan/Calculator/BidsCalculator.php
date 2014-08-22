<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Currency\Money;
use Zidisha\Loan\AcceptedBid;
use Zidisha\Loan\Bid;

class BidsCalculator {


    public function getAcceptedBids($bids, Money $loanUsdAmount)
    {
        $zero = Money::create(0, 'USD');
        $totalBidAmount = $zero;
        $acceptedBids = [];

        /** @var Bid $bid */
        foreach ($bids as $bid) {
            $bidAmount = $bid->getBidAmount();
            $missingAmount = $loanUsdAmount->subtract($totalBidAmount)->max($zero)->round(3);
            $totalBidAmount = $totalBidAmount->add($bidAmount);
            $acceptedAmount = $missingAmount->min($bidAmount);

            $acceptedBids[$bid->getId()] = new AcceptedBid($bid, $acceptedAmount);
        }

        // Sort by bid date, this changes the order of the transactions (amounts are the same)
        uasort(
            $acceptedBids,
            function (AcceptedBid $b1, AcceptedBid $b2) {
                return $b1->getBid()->getBidAt() <= $b2->getBid()->getBidAt();
            }
        );

        return $acceptedBids;
    }

    public function getChangedBids($oldAcceptedBids, $newAcceptedBids)
    {
        $changedBids = [];

        /** @var AcceptedBid $acceptedBid */
        foreach ($newAcceptedBids as $bidId => $acceptedBid) {
            $acceptedAmount = $acceptedBid->getAcceptedAmount();
            $bid = $acceptedBid->getBid();
            
            if (isset($oldAcceptedBids[$bidId])) {
                /** @var AcceptedBid $oldAcceptedBid */
                $oldAcceptedBid = $oldAcceptedBids[$bidId];
                $oldAcceptedAmount = $oldAcceptedBid->getAcceptedAmount();
                
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

    public function getLenderInterestRate($acceptedBids, Money $loanUsdAmount)
    {
        $totalAmount = Money::create(0);

        /** @var AcceptedBid $acceptedBid */
        foreach ($acceptedBids as $acceptedBid) {
            $acceptedAmount = $acceptedBid->getAcceptedAmount();
            $bid = $acceptedBid->getBid();

            if ($acceptedAmount->isPositive()) {
                $totalAmount = $totalAmount->add($acceptedAmount->multiply($bid->getInterestRate()));
            }
        }

        return round($totalAmount->ratio($loanUsdAmount), 2);
    }
}
