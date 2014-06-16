<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\Comment as BaseComment;

class Comment extends BaseComment
{
    private $children = array();

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(Comment $comment)
    {
        $this->children[] = $comment;
        return $this;
    }

    public function isRoot()
    {
        return $this->getLevel() == 0;
    }
}
