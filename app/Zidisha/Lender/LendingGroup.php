<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\LendingGroup as BaseLendingGroup;

class LendingGroup extends BaseLendingGroup
{

    public function isMember(Lender $lender)
    {
        $count = LendingGroupMemberQuery::create()
            ->filterByGroupId($this->getId())
            ->filterByMember($lender)
            ->filterByLeaved(false)
            ->count();

        return $count > 0;
    }

}
