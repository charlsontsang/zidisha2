<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LendingGroupComment as BaseLendingGroupComment;

class LendingGroupComment extends BaseLendingGroupComment
{
    public function setCommentReceiverId($comment, $id)
    {
        return $comment->setLendingGroupId($id);
    }

    public function getCommentReceiverId()
    {
        return $this->getLendingGroupId();
    }

    public function setCommentReceiver($comment, $receiver)
    {
        return $comment->setLendingGroup($receiver);
    }

    public function getCommentReceiver()
    {
        return $this->getLendingGroup();
    }
}
