<?php namespace Zidisha\Comment;

class BorrowerCommentService extends CommentService
{

    /**
     * @return BorrowerComment
     */
    protected function createComment()
    {
        return new BorrowerComment();
    }

    protected function getCommentQuery()
    {
        return new BorrowerCommentQuery();
    }
}
