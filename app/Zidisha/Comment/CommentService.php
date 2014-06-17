<?php
namespace Zidisha\Comment;

use Illuminate\Support\Facades\Input;
use Zidisha\Borrower\Borrower;
use Zidisha\Upload\Upload;
use Zidisha\User\User;

class CommentService
{
    public function postComment($data, User $user, Borrower $borrower)
    {
        $comment = new Comment();
        $comment->setUserId($user->getId());
        $comment->setMessage($data['message']);
        $comment->setBorrowerId($borrower->getId());
        $comment->setParentId(null);
        $comment->setLevel(0);
        $comment->save();

        $comment->setRootId($comment->getId());
        $comment->save();

        //Check if the posted comment has file
        if (Input::hasFile('file')) {
            //Check posted file is valid
            if (Input::file('file')->isValid()) {
                $filename = Input::file('file')->getClientOriginalName();
                $extension = Input::file('file')->getClientOriginalExtension();
                $mimeType = Input::file('file')->getMimeType();


                if ($extension == 'jpeg' || $extension == 'png' || $extension == 'jpg') {
                    $fileType = 'image';
                } else {
                    $fileType = 'document';
                }

                $upload = new Upload();
                $upload->setFilename($filename);
                $upload->setExtension($extension);
                $upload->setUserId($user->getId());
                $upload->setType($fileType);
                $upload->setMimeType($mimeType);
                $comment->addUpload($upload);
                $comment->save();

                Input::file('file')->move(public_path() . '/uploads', $filename);
            }
        }
        return $comment;
    }

    public function postReply($data, User $user, Borrower $borrower, Comment $parentComment)
    {
        $comment = new Comment();
        $comment->setUserId($user->getId());
        $comment->setMessage($data['message']);
        $comment->setBorrowerId($borrower->getId());
        $comment->setParentId($parentComment->getId());
        $comment->setLevel($parentComment->getLevel() + 1);
        $comment->setRootId($parentComment->getRootId());
        $comment->save();

        return $comment;
    }

    public function editComment($data, User $user, Comment $comment)
    {
        $comment->setMessage($data['message']);
        $comment->save();

        //Check if the posted comment has file
        if (Input::hasFile('file')) {
            //Check posted file is valid
            if (Input::file('file')->isValid()) {
                $filename = Input::file('file')->getClientOriginalName();
                $extension = Input::file('file')->getClientOriginalExtension();
                $mimeType = Input::file('file')->getMimeType();

                if ($extension == 'jpeg' || $extension == 'png' || $extension == 'jpg') {
                    $fileType = 'image';
                } else {
                    $fileType = 'document';
                }

                $upload = new Upload();
                $upload->setFilename($filename);
                $upload->setExtension($extension);
                $upload->setUserId($user->getId());
                $upload->setType($fileType);
                $upload->setMimeType($mimeType);
                $comment->addUpload($upload);
                $comment->save();

                Input::file('file')->move(public_path() . '/uploads', $filename);
            }
        }


    }

    public function deleteComment(Comment $comment)
    {
        $comment->setUserId(null);
        $comment->setMessage('This comment was deleted');

        if ($comment->isTranslated()) {
            $comment->setMessageTranslation(null);
            $comment->setTranslatorId(null);
        }

        if ($comment->getUploads()) {
            //TODO : Add a method to remove uploads accociated with comment.
        }
        $comment->save();
    }

    public function getPaginatedComments(Borrower $borrower, $page, $maxPerPage)
    {
        $roots = CommentQuery::create()
            ->filterByBorrowerId($borrower->getId())
            ->filterByLevel(0)
            ->orderById('desc')
            ->paginate($page, $maxPerPage);

        $comments = CommentQuery::create()
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

    public function translateComment($data, Comment $comment)
    {
        $comment->setMessageTranslation($data['message']);
        $comment->save();
    }

    public function deleteUpload(Comment $comment, Upload $upload)
    {
        $comment->removeUpload($upload);
        $comment->save();

        $upload->delete();
    }
} 