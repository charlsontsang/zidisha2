<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\Group as BaseGroup;

class Group extends BaseGroup
{

    public function isMember(Lender $lender)
    {
        $count = GroupMemberQuery::create()
            ->filterByGroupId($this->getId())
            ->filterByMember($lender)
            ->filterByLeaved(false)
            ->count();

        return $count > 0;
    }
}
