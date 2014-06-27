<?php

namespace Zidisha\Lender;


class GiftCardService
{

    public function validateCode($redemptionCode)
    {
        $count = CardQuery::create()
            ->filterByCardCode($redemptionCode)
            ->count();
        if ($count > 1) {
            return 3; // 3 if duplicate codes
        }
        $card = CardQuery::create()
            ->filterByCardCode($redemptionCode)
            ->findOne();
        if (!$card) {
            return 2; //2 if code is invalid
        }
        if ($card->getStatus() == 1) {
            return 1; /*   1 for card code is valid and is purchaged*/
        } elseif ($card->getStatus() == 0) {
            return 0; /*   0 for card code is valid and is not purchaged*/
        }
    }

}
