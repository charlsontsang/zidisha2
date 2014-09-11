<?php
namespace Zidisha\Comment;

use Zidisha\Loan\Loan;
use Zidisha\User\User;

class LoanFeedbackCommentService extends CommentService
{
    public function editComment($data, User $user, Comment $comment, $files = [])
    {
        if ($comment->isRoot()) {
            $comment->setRating($data['rating']);
        }
        
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
        
        if (!array_get($data, 'parent_id')) {
            $comment->setRating($data['rating']);
        };

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
}
