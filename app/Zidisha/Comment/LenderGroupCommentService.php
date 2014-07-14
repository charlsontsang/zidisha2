<?php
namespace Zidisha\Comment;


class LenderGroupCommentService extends CommentService
{

    /**
     * @return Comment
     */
    protected function createComment()
    {
        return new LenderGroupComment();
    }

    protected function getCommentQuery()
    {
        return new LenderGroupCommentQuery();
    }
}
