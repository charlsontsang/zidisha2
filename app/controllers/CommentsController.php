<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentQuery;

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

        $comment = $this->commentService->postComment(compact('message'), $user, $borrower);

        Flash::success('Your comment was posted');
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
        if ($message == '' || !$comment || $comment->getUserId() != $user->getId()) {
            App::abort(404, 'Bad Request');
        }

        $this->commentService->editComment(compact('message'), $comment);

        Flash::success('Your comment was edited');
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

        if (!$borrower || $message == '' || !$parentComment) {
            dd('we are here');
            App::abort(404, 'Bad Request');
        }

        $comment = $this->commentService->postReply(compact('message'), $user, $borrower, $parentComment);

        Flash::success('Your reply was posted');
        return Redirect::backAppend("#comment-" . $comment->getId());
    }

    public function postDelete()
    {
        $commentId = Input::get('comment_id');

        $comment = CommentQuery::create()
            ->filterById($commentId)
            ->findOne();

        $user = \Auth::user();

        if (!$comment || $comment->getUserId() != $user->getId()) {
            App::abort(404, 'Bad Request');
        }

        $this->commentService->deleteComment($comment);

        Flash::success('Your comment was deleted');
        return Redirect::back();

    }
}