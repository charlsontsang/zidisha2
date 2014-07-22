<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\BorrowerCommentQuery;

class BorrowerCommentController extends CommentsController
{

    protected function getReceiverQuery()
    {
        return new BorrowerQuery();
    }

    /**
     * @return \Zidisha\Comment\CommentService
     */
    protected function getService()
    {
        return App::make('Zidisha\Comment\BorrowerCommentService');
    }

    protected function getCommentQuery()
    {
        return new BorrowerCommentQuery();
    }
}
