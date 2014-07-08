<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\Group as BaseGroup;

class Group extends BaseGroup
{

    public function isMember(Lender $lender)
    {
        $member = GroupMemberQuery::create()
            ->filterByGroupId($this->getId())
            ->filterByMember($lender)
            ->filterByLeaved(false)
            ->findOne();
        if($member){
            return true;
        }
        return false;
    }
}
