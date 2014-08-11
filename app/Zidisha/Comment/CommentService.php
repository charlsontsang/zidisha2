<?php
namespace Zidisha\Comment;

use Zidisha\Borrower\Borrower;
use Zidisha\Upload\Upload;
use Zidisha\User\User;

abstract class CommentService
{
    protected $receiver;

    /**
     * @return Comment
     */
    protected abstract function createComment();

    /**
     * @return CommentQuery
     */
    protected abstract function createCommentQuery();

    protected abstract function notify(Comment $comment);

    public function postComment($data, User $user,CommentReceiverInterface $receiver, $files = [])
    {
        //Abstract
        $comment = $this->createComment();
        $comment->setUserId($user->getId());
        $comment->setMessage($data['message']);
        $comment->setCommentReceiverId($comment, $receiver->getCommentReceiverId());
        $comment->setParentId(null);
        $comment->setLevel(0);
        $comment->save();

        $comment->setRootId($comment->getId());
        $comment->save();

        if ($files) {
            foreach ($files as $file) {
                $upload = Upload::createFromFile($file);
                $upload->setUser($user);
                $upload->save();
                $comment->addUpload($upload);
            }
            $comment->save();
        }

        $this->notify($comment);

        return $comment;
    }

    public function postReply($data, User $user, CommentReceiverInterface $receiver, Comment $parentComment, $files = [])
    {
        $comment = $this->createComment();
        $comment->setUserId($user->getId());
        $comment->setMessage($data['message']);
        $comment->setCommentReceiverId($comment, $receiver->getCommentReceiverId());
        $comment->setParentId($parentComment->getId());
        $comment->setLevel($parentComment->getLevel() + 1);
        $comment->setRootId($parentComment->getRootId());
        $comment->save();

        if ($files) {
            foreach ($files as $file) {
                $upload = Upload::createFromFile($file);
                $upload->setUser($user);
                $upload->save();
                $comment->addUpload($upload);

            }
            $comment->save();
        }

        $this->notify($comment);

        return $comment;
    }

    public function editComment($data, User $user, Comment $comment, $files = [])
    {
        $comment->setMessage($data['message']);
        $comment->save();

        if ($files) {
            foreach ($files as $file) {
                $upload = Upload::createFromFile($file);
                $upload->setUser($user);
                $comment->addUpload($upload);

            }
            $comment->save();
        }
    }

    public function deleteComment(Comment $comment)
    {
        $comment->setUserId(null);
        $comment->setMessage('This comment was deleted');

        $comment->setMessageTranslation(null);
        $comment->setTranslatorId(null);

        $comment->save();

        if ($this->isUploadsAllowed()) {
            foreach ($comment->getUploads() as $upload) {
                $comment->removeUpload($upload);
                $comment->save();
                $upload->delete();
            }
        }
    }

    public function getPaginatedComments(CommentReceiverInterface $receiver, $page, $maxPerPage)
    {
        $roots = $this->createCommentQuery()
            ->filterByReceiverId($receiver->getCommentReceiverId())
            ->filterByLevel(0)
            ->orderById('desc')
            ->paginate($page, $maxPerPage);

        $comments = $this->createCommentQuery()
            ->filterByRootId($roots->toKeyValue('id', 'id'))
            ->filterByLevel(['min' => 1])
            ->orderById('asc')
            ->find();

        $idToComments = [];

        foreach ($roots as $root) {
            $idToComments[$root->getId()] = $root;
        }

        foreach ($comments as $comment) {
            $idToComments[$comment->getId()] = $comment;
        }

        foreach ($comments as $comment) {
            if (!$comment->isRoot()) {
                $parentComment = $idToComments[$comment->getParentId()];
                $parentComment->addChild($comment);
            }
        }

        return $roots;
    }

    public function translateComment($data, Comment $comment, User $user)
    {
        $comment->setMessageTranslation($data['message']);
        $comment->setTranslator($user);
        $comment->save();
    }

    public function deleteUpload(Comment $comment, Upload $upload)
    {
        $comment->removeUpload($upload);
        $comment->save();

        $upload->delete();
    }

    public function isUploadsAllowed()
    {
        return true;
    }
}
