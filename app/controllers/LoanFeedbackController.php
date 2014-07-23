<?php

use Zidisha\Comment\Form\LoanFeedbackEditCommentForm;
use Zidisha\Comment\Form\LoanFeedbackPostCommentForm;
use Zidisha\Comment\Form\ReplyCommentForm;
use Zidisha\Comment\Form\TranslateCommentForm;
use Zidisha\Comment\LoanFeedbackCommentQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;

class LoanFeedbackController extends CommentsController
{
    protected $allowUploads = false;

    /**
     * @return \Zidisha\Comment\CommentService
     */
    protected function getService()
    {
        return App::make('Zidisha\Comment\LoanFeedbackCommentService');
    }

    protected function getReceiverQuery()
    {
        return new LoanQuery();
    }

    protected function getCommentQuery()
    {
        return new LoanFeedbackCommentQuery();
    }

    protected function redirect($comment)
    {
        return Redirect::backAppend("#feedback-" . $comment->getId());
    }

    /**
     * @param $receiver
     */
    protected function validateReceiver($receiver)
    {
        if (!($receiver->getStatus() == Loan::DEFAULTED || $receiver->getStatus() == Loan::REPAID)) {
            App::abort(404, 'Loan is not completed');
        }
    }

    protected function getPostCommentForm()
    {
        return new LoanFeedbackPostCommentForm();
    }

    protected function getEditCommentForm()
    {
        return new LoanFeedbackEditCommentForm();
    }

    protected function getReplyCommentForm()
    {
        return new ReplyCommentForm();
    }

    protected function getTranslateCommentForm()
    {
        return new TranslateCommentForm();
    }
}
