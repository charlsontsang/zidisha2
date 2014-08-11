<?php

use Illuminate\Support\Facades\Input;
use Zidisha\Comment\CommentService;
use Zidisha\Comment\Form\EditCommentForm;
use Zidisha\Comment\Form\PostCommentForm;
use Zidisha\Comment\Form\ReplyCommentForm;
use Zidisha\Comment\Form\TranslateCommentForm;
use Zidisha\Flash\Flash;
use Zidisha\Upload\UploadQuery;

abstract class CommentsController extends BaseController
{
    protected $service;

    protected $allowUploads = true;

    public function __construct()
    {
        $this->service = $this->getService();
    }

    public function postComment($id)
    {
        $receiver = $this->getReceiverQuery()
            ->filterById($id)
            ->findOne();

        $user = \Auth::user();

        if (!$receiver || !$user) {
            App::abort(404, 'Bad Request');
        }

        $this->validateReceiver($receiver);

        $postCommentForm = $this->getPostCommentForm();
        $postCommentForm->handleRequest(Request::instance());

        if (!$postCommentForm->isValid()) {
            Flash::success('Input not valid');
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postComment($postCommentForm->getData(), $user, $receiver, $files);

        Flash::success(\Lang::get('borrower.comments.flash.post-success'));
        return $this->redirect($comment);
    }

    protected function getInputFiles()
    {
        $files = [];
        if (\Input::hasFile('file') && $this->service->isUploadsAllowed()) {
            foreach (\Input::file('file') as $file) {
                if (!empty($file)) {
                    if ($file->isValid() && $file->getSize() < Config::get('image.allowed-file-size')) {
                        $files[] = $file;
                    } else {
                        Flash::error(\Lang::get('borrower.comments.flash.file-not-valid'));
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

        $editCommentForm = $this->getEditCommentForm();
        $editCommentForm->handleRequest(Request::instance());

        if (!$editCommentForm->isValid()) {
            Flash::error('Please enter proper inputs');
            return Redirect::back();
        }

        $files = $this->getInputFiles();

        $this->service->editComment($editCommentForm->getData(), $user, $comment, $files);

        Flash::success(\Lang::get('borrower.comments.flash.edit-success'));
        return $this->redirect($comment);
    }

    public function postReply($id)
    {
        $message = trim(Input::get('message'));
        $parentId = Input::get('parent_id');

        $receiver = $this->getReceiverQuery()
            ->filterById($id)
            ->findOne();

        $user = \Auth::user();

        $parentComment = $this->getCommentQuery()
            ->filterById($parentId)
            ->findOne();

        if (!$receiver || $message == '' || !$parentComment || $parentComment->isOrphanDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $this->validateReceiver($receiver);

        $replyCommentForm = $this->getReplyCommentForm();
        $replyCommentForm->handleRequest(Request::instance());

        if (!$replyCommentForm->isValid()) {
            Flash::success('Input not valid');
            return Redirect::back();
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postReply($replyCommentForm->getData(), $user, $receiver, $parentComment, $files);

        Flash::success(\Lang::get('borrower.comments.flash.reply-success'));
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

        Flash::success(\Lang::get('borrower.comments.flash.delete-success'));
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

        $translateCommentForm = $this->getTranslateCommentForm();
        $translateCommentForm->handleRequest(Request::instance());

        if (!$translateCommentForm->isValid()) {
            Flash::success('Input not proper.');
            return Redirect::back();
        }

        $this->service->translateComment($translateCommentForm->getData(), $comment, $user);

        Flash::success(\Lang::get('borrower.comments.flash.translate-success'));
        return $this->redirect($comment);
    }

    public function postDeleteUpload()
    {
        $comment = $this->getCommentQuery()
            ->filterById(\Input::get('comment_id'))
            ->findOne();

        $upload = UploadQuery::create()->filterById(\Input::get('upload_id'))->findOne();

        $user = \Auth::user();

        if (!$comment || !$upload || $comment->getUserId() != $user->getId()) {
            App::abort(404, 'Bad Request');
        }

        $this->service->deleteUpload($comment, $upload);

        Flash::success(\Lang::get('borrower.comments.flash.file-deleted'));
        return Redirect::back();
    }

    protected abstract function getReceiverQuery();

    /**
     * @return CommentService
     */
    protected abstract function getService();

    protected abstract function getCommentQuery();

    protected function redirect($comment)
    {
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    protected function validateReceiver($receiver)
    {
    }

    protected function getPostCommentForm()
    {
        return new PostCommentForm();
    }

    protected function getEditCommentForm()
    {
        return new EditCommentForm();
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
