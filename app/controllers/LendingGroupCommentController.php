<?php

use Zidisha\Comment\LendingGroupCommentQuery;
use Zidisha\Lender\LendingGroupQuery;

class LendingGroupCommentController extends CommentsController
{
    protected function getReceiverQuery()
    {
        return new LendingGroupQuery();
    }

    protected function getService()
    {
        return App::make('Zidisha\Comment\LendingGroupCommentService');
    }

    protected function getCommentQuery()
    {
        return new LendingGroupCommentQuery();
    }
}
