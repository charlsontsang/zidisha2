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
}
