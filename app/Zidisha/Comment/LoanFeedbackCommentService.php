<?php
namespace Zidisha\Comment;

use Zidisha\User\User;

class LoanFeedbackCommentService extends CommentService
{
    public function postComment($data, User $user,CommentReceiverInterface $receiver, $files = [])
    {
        $comment = parent::postComment($data, $user, $receiver, $files);
        $comment->setRating($data['rating']);
        $comment->save();

        return $comment;
    }

    public function editComment($data, User $user, Comment $comment, $files = [])
    {
        $comment->setRating($data['rating']);
        parent::editComment($data, $user, $comment, $files);
    }

    public function deleteComment(Comment $comment)
    {
        $comment->setRating(null);
        parent::deleteComment($comment);
    }

    /**
     * @return LoanFeedbackComment
     */
    protected function createComment()
    {
        return new LoanFeedbackComment();
    }

    /**
     * @return CommentQuery
     */
    protected function createCommentQuery()
    {
        return LoanFeedbackCommentQuery::create();
    }

    protected function notify(Comment $comment)
    {
        return;
    }

    public function isUploadsAllowed()
    {
        return false;
    }

    public function hasGivenFeedback($userId, $loanId)
    {
        return LoanFeedbackCommentQuery::create()
            ->filterByReceiverId($loanId)
            ->filterByUserId($userId)
            ->count();
    }
}
