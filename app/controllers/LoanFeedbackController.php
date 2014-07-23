<?php

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

    public function postComment($id)
    {
        if (!Input::has('rating')) {
            App::abort(404, 'Bad Request');
        }

        $rating = Input::get('rating');

        $message = trim(Input::get('message'));

        $receiver = $this->getReceiverQuery()
            ->filterById($id)
            ->findOne();

        $user = \Auth::user();

        if (!$receiver || $message == '' || !$user) {
            App::abort(404, 'Bad Request');
        }

        if (! ($receiver->getStatus() == Loan::DEFAULTED || $receiver->getStatus() == Loan::REPAID)) {
            App::abort(404, 'Loan is not completed');
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postComment(compact('message', 'rating'), $user, $receiver, $files);

        Flash::success(\Lang::get('comments.flash.post'));
        return $this->redirect($comment);
    }

    public function postEdit()
    {
        $commentId = Input::get('comment_id');
        $rating = Input::get('rating');
        $message = trim(Input::get('message'));

        $comment = $this->getCommentQuery()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();
        if ($message == '' || !$comment || $comment->getUserId() != $user->getId() || $comment->isOrphanDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $files = $this->getInputFiles();

        $this->service->editComment(compact('message', 'rating'), $user, $comment, $files);

        Flash::success(\Lang::get('comments.flash.edit'));
        return $this->redirect($comment);
    }

    public function postReply($id)
    {
        $message = trim(Input::get('message'));
        $parentId = Input::get('parent_id');

        $receiver = $this->getReceiverQuery()
            ->filterById(Input::get('receiver_id'))
            ->findOne();

        $user = \Auth::user();

        $parentComment = $this->getCommentQuery()
            ->filterById($parentId)
            ->findOne();

        if (!$receiver || $message == '' || !$parentComment || $parentComment->isOrphanDeleted()) {
            App::abort(404, 'Bad Request');
        }

        if (! ($receiver->getStatus() == Loan::DEFAULTED || $receiver->getStatus() == Loan::REPAID)) {
            App::abort(404, 'Loan is not completed');
        }

        $comment = $this->service->postReply(compact('message'), $user, $receiver, $parentComment);

        Flash::success(\Lang::get('comments.flash.reply'));
        return $this->redirect($comment);
    }

    public function postDelete()
    {
        $commentId = Input::get('comment_id');

        $comment = $this->getCommentQuery()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();

        if (!$comment || $comment->getUserId() != $user->getId() || $comment->isOrphanDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $this->service->deleteComment($comment);

        Flash::success(\Lang::get('comments.flash.delete'));
        return Redirect::back();
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
}
