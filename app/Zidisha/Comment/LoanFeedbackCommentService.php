<?php
namespace Zidisha\Comment;

use Zidisha\User\User;

class LoanFeedbackCommentService extends CommentService
{
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
     * @param array $data
     * @return LoanFeedbackComment
     */
    protected function createComment($data = [])
    {
        $comment = new LoanFeedbackComment();
        $comment->setRating($data['rating']);

        return $comment;
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
