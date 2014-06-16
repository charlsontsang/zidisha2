<?php
namespace Zidisha\Comment;

use Zidisha\Borrower\Borrower;
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

    public function editComment($data, Comment $comment)
    {
        $comment->setMessage($data['message']);
        $comment->save();
    }

    public function deleteComment(Comment $comment)
    {
        $comment->setUserId(null);
        $comment->setMessage('This comment was deleted');
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
} 