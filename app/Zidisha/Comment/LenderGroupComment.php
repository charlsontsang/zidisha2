<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LenderGroupComment as BaseLenderGroupComment;

class LenderGroupComment extends BaseLenderGroupComment
{


    public function setCommentReceiverId($comment, $id)
    {
        $comment->setLendingGroupId($id);
    }

    public function getCommentReceiverId()
    {
        return $this->getLendingGroupId();
    }

    public function setCommentReceiver($comment, $receiver)
    {
        $comment->setLendingGroup($receiver);
    }

    public function getCommentReceiver()
    {
        return $this->getLendingGroup();
    }
}
