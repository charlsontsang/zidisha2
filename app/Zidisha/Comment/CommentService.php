<?php
namespace Zidisha\Comment;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\Borrower;
use Zidisha\Upload\Upload;
use Zidisha\User\User;

abstract class CommentService
{
    protected $receiver;

    /**
     * @return Comment
     */
    protected abstract function createComment($data = []);

    /**
     * @return CommentQuery
     */
    protected abstract function createCommentQuery();

    protected abstract function notify(Comment $comment);

    public function postComment($data, User $user,CommentReceiverInterface $receiver, $files = [])
    {
        //Abstract
        $message = stripslashes(strip_tags(trim($data['message'])));
        $comment = $this->createComment($data);
        $comment->setUserId($user->getId());
        $comment->setMessage($message);
        $comment->setCommentReceiverId($comment, $receiver->getCommentReceiverId());
        $comment->setParentId(null);
        $comment->setLevel(0);
        $comment->save();

        $comment->setRootId($comment->getId());
        $comment->save();

        if ($files) {
            foreach ($files as $file) {
                $upload = new Upload();
                $upload->setUser($user);
                $upload->createFromFile($file);
                $upload->save();
                $upload->setFileName( $upload->getId() . $upload->getFileName());
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
        $comment = $this->createComment($data);
        $message = stripslashes(strip_tags(trim($data['message'])));
        $comment = $this->createComment();
        $comment->setUserId($user->getId());
        $comment->setMessage($message);
        $comment->setCommentReceiverId($comment, $receiver->getCommentReceiverId());
        $comment->setParentId($parentComment->getId());
        $comment->setLevel($parentComment->getLevel() + 1);
        $comment->setRootId($parentComment->getRootId());
        $comment->save();

        if ($files) {
            foreach ($files as $file) {
                $upload = new Upload();
                $upload->setUser($user);
                $upload->createFromFile($file);
                $upload->save();
                $upload->setFileName( $upload->getId() . $upload->getFileName());
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
                $upload = new Upload();
                $upload->setUser($user);
                $upload->createFromFile($file);
                $upload->save();
                $upload->setFileName( $upload->getId() . $upload->getFileName());
                $upload->save();
                $comment->addUpload($upload);
            }
            $comment->save();
        }
    }

    public function deleteComment(Comment $comment)
    {
        if ($this->isUploadsAllowed()) {
            foreach ($comment->getUploads() as $upload) {
                $comment->removeUpload($upload);
                $comment->save();
                $upload->delete();
            }
        }

        $childCommentCount = $this->createCommentQuery()
            ->filterByParentId($comment->getId())
            ->count();

        if (!$childCommentCount) {
            $comment->delete();
        } else {
            $comment->setUserId(null);
            $comment->setMessage('This comment has been deleted');
            $comment->setRemoved(true);
            $comment->setMessageTranslation(null);
            $comment->setTranslatorId(null);

            $comment->save();
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
