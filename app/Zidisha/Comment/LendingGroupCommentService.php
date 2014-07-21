<?php
namespace Zidisha\Comment;


class LendingGroupCommentService extends CommentService
{

    /**
     * @return Comment
     */
    protected function createComment()
    {
        return new LendingGroupComment();
    }

    protected function createCommentQuery()
    {
        return LendingGroupCommentQuery::create();
    }

    protected function notify(Comment $comment)
    {
        // TODO: Implement notify() method.
    }
}
