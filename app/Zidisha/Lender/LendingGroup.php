<?php

namespace Zidisha\Lender;

use Zidisha\Comment\CommentReceiverInterface;
use Zidisha\Lender\Base\LendingGroup as BaseLendingGroup;

class LendingGroup extends BaseLendingGroup implements CommentReceiverInterface
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

    public function isLeader(Lender $lender)
    {
        return $this->getLeader() == $lender;
    }

    public function getCommentReceiverId()
    {
        return $this->getId();
    }
}
