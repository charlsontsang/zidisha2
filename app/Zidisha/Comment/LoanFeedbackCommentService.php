<?php
namespace Zidisha\Comment;

use Zidisha\User\User;

class LoanFeedbackCommentService extends CommentService
{
    public function postComment($data, User $user,CommentReceiverInterface $receiver, $files = [])
    {
        $comment = $this->createComment();
        $comment->setUserId($user->getId());
        $comment->setMessage($data['message']);
        $comment->setRatingType($data['rating']);
        $comment->setCommentReceiverId($comment, $receiver->getCommentReceiverId());
        $comment->setParentId(null);
        $comment->setLevel(0);
        $comment->save();

        $comment->setRootId($comment->getId());
        $comment->save();

        $this->notify($comment);

        return $comment;
    }

    public function editComment($data, User $user, Comment $comment, $files = [])
    {
        $comment->setMessage($data['message']);
        $comment->setRatingType($data['rating']);
        $comment->save();
    }

    public function deleteComment(Comment $comment)
    {
        $comment->setUserId(null);
        $comment->setMessage('This comment was deleted');

        $comment->setMessageTranslation(null);
        $comment->setTranslatorId(null);
        $comment->setRatingType(null);

        $comment->save();
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
}
