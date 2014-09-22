<?php

use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            Flash::error('common.validation.error');
            return \Redirect::back();
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postComment($postCommentForm->getData(), $user, $receiver, $files);

        Flash::success(\Lang::get('common.comments.flash.post-success'));
        return $this->redirect($comment);
    }

    protected function getInputFiles()
    {
        $files = [];
        if (\Input::hasFile('file') && $this->service->isUploadsAllowed()) {
            foreach (\Input::file('file') as $file) {
                if (!empty($file)) {
                    /** @var UploadedFile $file */
                    if ($file->isValid() 
                        && $file->getPath() != ''
                        && in_array($file->guessExtension(), ['jpeg', 'png', 'gif', 'bmp'])
                        && $file->getSize() < Config::get('image.allowed-file-size'))
                    {
                        $files[] = $file;
                    } else {
                        Flash::error(\Lang::get('common.comments.flash.file-not-valid'));
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

        $editCommentForm = $this->getEditCommentForm($comment);
        $editCommentForm->handleRequest(Request::instance());

        if (!$editCommentForm->isValid()) {
            Flash::error('Please enter proper inputs');
            return Redirect::back();
        }

        $files = $this->getInputFiles();

        $this->service->editComment($editCommentForm->getData(), $user, $comment, $files);

        Flash::success(\Lang::get('common.comments.flash.edit-success'));
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
            Flash::error('common.validation.error');
            return Redirect::back();
        }

        $files = $this->getInputFiles();

        $comment = $this->service->postReply($replyCommentForm->getData(), $user, $receiver, $parentComment, $files);

        Flash::success(\Lang::get('common.comments.flash.reply-success'));
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

        Flash::success(\Lang::get('common.comments.flash.delete-success'));
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
        if ($message == '' || !$comment || !($user->isAdmin() || $user->isLender())) {
            App::abort(404, 'Bad Request');
        }

        $translateCommentForm = $this->getTranslateCommentForm();
        $translateCommentForm->handleRequest(Request::instance());

        if (!$translateCommentForm->isValid()) {
            Flash::error('common.validation.error');
            return Redirect::back();
        }

        $this->service->translateComment($translateCommentForm->getData(), $comment, $user);

        Flash::success(\Lang::get('common.comments.flash.translate-success'));
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

        Flash::success(\Lang::get('common.comments.flash.file-deleted'));
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

    protected function getEditCommentForm($comment)
    {
        return new EditCommentForm($comment);
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
