<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\InviteVisit as BaseInviteVisit;

class InviteVisit extends BaseInviteVisit
{

    public function getHumanShareType()
    {
        $shareTypes = array(
            1 => 'email',
            2 => 'twitter',
            3 => 'facebook',
        );

        return array_get($shareTypes, $this->getShareType(), 'website');
    }

}
