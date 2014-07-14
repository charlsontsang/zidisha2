<?php

use Illuminate\Support\Facades\Input;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Comment\LendingGroupCommentQuery;
use Zidisha\Comment\LendingGroupCommentService;
use Zidisha\Flash\Flash;
use Zidisha\Lender\LendingGroupQuery;

class CommentsController extends BaseController
{
    protected $commentType;

    protected $service;

    public function __construct()
    {
        if ( !Input::has('commentType') ) {
            App::abort(404, 'Bad Request');
        }

        $this->commentType = Input::get('commentType');
        $this->service = $this->getService($this->commentType);
    }

    public function postComment()
    {
        if (!Input::has('receiver_id')) {
            App::abort(404, 'Bad Request');
        }

        $message = trim(Input::get('message'));
        $receiverId = Input::get('receiver_id');

        $receiver = $this->getReceiverQuery()
            ->filterById($receiverId)
            ->findOne();

        $user = \Auth::user();

        if (!$receiver || $message == '') {
            App::abort(404, 'Bad Request');
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postComment(compact('message'), $user, $receiver, $files);

        Flash::success(\Lang::get('comments.flash.post'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    protected function getInputFiles()
    {
        $files = [];
        if (\Input::hasFile('file')) {
            foreach (\Input::file('file') as $file) {
                if (!empty($file)) {
                    if ($file->isValid() && $file->getSize() < Config::get('image.allowed-file-size')) {
                        $files[] = $file;
                    } else {
                        Flash::error(\Lang::get('comments.flash.file-not-valid'));
                    }
                }
            }
            return $files;
        }
        return $files;
    }

    public function postEdit()
    {
        $commentId = Input::get('comment_id');
        $message = trim(Input::get('message'));

        $comment = $this->getCommentQuery()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();
        if ($message == '' || !$comment || $comment->getUserId() != $user->getId() || $comment->isOrphanDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $files = $this->getInputFiles();

        $this->service->editComment(compact('message'), $user, $comment, $files);

        Flash::success(\Lang::get('comments.flash.edit'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postReply()
    {
        if (!Input::has('receiver_id')) {
            App::abort(404, 'Bad Request');
        }

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

        $comment = $this->service->postReply(compact('message'), $user, $receiver, $parentComment);

        Flash::success(\Lang::get('comments.flash.reply'));
        return Redirect::backAppend("#comment-" . $comment->getId());
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

    public function postTranslate()
    {
        $commentId = Input::get('comment_id');
        $message = trim(Input::get('message'));

        $comment = $this->getCommentQuery()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();
        $userRole = $user->getRole();
        if ($message == '' || !$comment || $userRole != 'lender') {
            App::abort(404, 'Bad Request');
        }

        $this->service->translateComment(compact('message'), $comment);

        Flash::success(\Lang::get('comments.flash.translate'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postDeleteUpload()
    {
        $comment = $this->getCommentQuery()
            ->filterById(\Input::get('receiver_id'))
            ->findOne();

        $upload = \Zidisha\Upload\UploadQuery::create()->filterById(\Input::get('upload_id'))->findOne();

        $user = \Auth::user();

        if (!$comment || !$upload || $comment->getUserId() != $user->getId()) {
            App::abort(404, 'Bad Request');
        }

        $this->service->deleteUpload($comment, $upload);

        Flash::success(\Lang::get('comments.flash.file-deleted'));
        return Redirect::back();
    }

    private function getReceiverQuery()
    {
        if ($this->commentType == 'lendingGroupComment') {
            return new LendingGroupQuery();
        } elseif ($this->commentType == 'borrowerComment') {
            return new BorrowerQuery();
        }
    }

    private function getService()
    {
        if ($this->commentType == 'lendingGroupComment') {
            return new LendingGroupCommentService();
        } elseif ($this->commentType == 'borrowerComment') {
            return new BorrowerCommentService();
        }
    }

    private function getCommentQuery()
    {
        if ($this->commentType == 'lendingGroupComment') {
            return new LendingGroupCommentQuery();
        } elseif ($this->commentType == 'borrowerComment') {
            return new BorrowerCommentQuery();
        }
    }
}
