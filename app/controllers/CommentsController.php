<?php

use Illuminate\Support\Facades\Input;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentQuery;
use Zidisha\Flash\Flash;

class CommentsController extends BaseController
{

    /**
     * @var Zidisha\Comment\CommentService
     */
    private $commentService;

    public function __construct(\Zidisha\Comment\CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function postComment()
    {
        $message = trim(Input::get('message'));
        $borrower = BorrowerQuery::create()
            ->filterById(Input::get('borrower_id'))
            ->findOne();

        $user = \Auth::user();

        if (!$borrower || $message == '') {
            App::abort(404, 'Bad Request');
        }

        //Check if the posted comment has file and is valid
        if (!\Input::hasFile('file') || !\Input::file('file')->isValid())
        {
            Flash::error(\Lang::get('comments.flash.file-not-valid'));
        }

        $comment = $this->commentService->postComment(compact('message'), $user, $borrower);

        Flash::success(\Lang::get('comments.flash.post'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postEdit()
    {
        $commentId = Input::get('comment_id');
        $message = trim(Input::get('message'));

        $comment = CommentQuery::create()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();
        if ($message == '' || !$comment || $comment->getUserId() != $user->getId() || $comment->isDeleted()) {
            App::abort(404, 'Bad Request');
        }

        //Check if the posted comment has file and is valid
        if (!\Input::hasFile('file') || !\Input::file('file')->isValid())
        {
            Flash::error(\Lang::get('comments.flash.file-not-valid'));
        }

        $this->commentService->editComment(compact('message'), $user, $comment);

        Flash::success(\Lang::get('comments.flash.edit'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postReply()
    {
        $message = trim(Input::get('message'));
        $parentId = Input::get('parent_id');

        $borrower = BorrowerQuery::create()
            ->filterById(Input::get('borrower_id'))
            ->findOne();

        $user = \Auth::user();

        $parentComment = CommentQuery::create()
            ->filterById($parentId)
            ->findOne();

        if (!$borrower || $message == '' || !$parentComment || $parentComment->isDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $comment = $this->commentService->postReply(compact('message'), $user, $borrower, $parentComment);

        Flash::success(\Lang::get('comments.flash.reply'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postDelete()
    {
        $commentId = Input::get('comment_id');

        $comment = CommentQuery::create()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();

        if (!$comment || $comment->getUserId() != $user->getId() || $comment->isDeleted()) {
            App::abort(404, 'Bad Request');
        }

        $this->commentService->deleteComment($comment);

        Flash::success(\Lang::get('comments.flash.delete'));
        return Redirect::back();
    }

    public function postTranslate()
    {
        $commentId = Input::get('comment_id');
        $message = trim(Input::get('message'));

        $comment = CommentQuery::create()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();
        $userRole = $user->getRole();
        if ($message == '' || !$comment || $userRole != 'lender') {
            App::abort(404, 'Bad Request');
        }

        $this->commentService->translateComment(compact('message'), $comment);

        Flash::success(\Lang::get('comments.flash.translate'));
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postDeleteUpload()
    {
        $comment = CommentQuery::create()->filterById(\Input::get('comment_id'))->findOne();
        $upload = \Zidisha\Upload\UploadQuery::create()->filterById(\Input::get('upload_id'))->findOne();

        if(!$comment || !$upload){
            App::abort(404, 'Bad Request');
        }

        $this->commentService->deleteUpload($comment, $upload);

        Flash::success(\Lang::get('comments.flash.file-deleted'));
        return Redirect::back();
    }
}