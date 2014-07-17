<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\BorrowerComment as BaseBorrowerComment;
use Zidisha\Upload\Upload;

class BorrowerComment extends BaseBorrowerComment
{

    public function setCommentReceiverId($comment, $id)
    {
        return $comment->setBorrowerId($id);
    }

    public function getCommentReceiverId()
    {
        return $this->getCommentReceiverId();
    }

    public function setCommentReceiver($comment, $receiver)
    {
        return $comment->setBorrower($receiver);
    }

    public function getCommentReceiver()
    {
        return $this->getBorrower();
    }

    public function initUploadsWithUglyFixButItWorks()
    {
        $this->initUploads();
        $this->collUploadsPartial = false;
    }

    public function addUploadWithUglyFixButItWorks(Upload $upload)
    {
        if (!$this->getUploads()->contains($upload)) {
            // only add it if the **same** object is not already associated
            $this->collUploads->push($upload);
        }

        return $this;
    }

}
