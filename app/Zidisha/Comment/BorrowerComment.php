<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\BorrowerComment as BaseBorrowerComment;

class BorrowerComment extends BaseBorrowerComment
{

    public function setCommentReceiverId($comment, $id)
    {
        $comment->setBorrowerId($id);
    }

    public function getCommentReceiverId()
    {
        return $this->getCommentReceiverId();
    }

    public function setCommentReceiver($comment, $receiver)
    {
        $comment->setBorrower($receiver);
    }

    public function getCommentReceiver()
    {
        return $this->getBorrower();
    }

}
