<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LoanFeedbackComment as BaseLoanFeedbackComment;

class LoanFeedbackComment extends BaseLoanFeedbackComment
{
    const POSITIVE = 'positive';
    const NEUTRAL  = 'neutral';
    const NEGATIVE = 'negative';

    public function setCommentReceiverId($comment, $id)
    {
        return $comment->setLoanId($id);
    }

    public function getCommentReceiverId()
    {
        return $this->getLoanId();
    }

    public function setCommentReceiver($comment, $receiver)
    {
        return $comment->setLoan($receiver);
    }

    public function getCommentReceiver()
    {
        return $this->getLoan();
    }
}
