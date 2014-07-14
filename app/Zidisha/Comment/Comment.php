<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\Comment as BaseComment;

abstract class Comment extends BaseComment
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

    public function getFacebookUrl()
    {
        $url = \Request::url() . "#comment-" . $this->getId();
        $relative_share_url = str_replace("https://www.", "", $url);
        return "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode($relative_share_url);
    }

    public function getTwitterUrl()
    {
        $url = \Request::url() . "#comment-" . $this->getId();

        $message = $this->isTranslated() ? $this->getMessageTranslation() : $this->getMessage();

        $username = $this->getUser()->getUsername();

        $twitterParams = array(
            "url" => $url,
            "text" => "$message -- $username",
        );

        return "http://twitter.com/share?" . http_build_query($twitterParams);
    }

    public function isOrphanDeleted()
    {
        return $this->getUser() ? false : true;
    }

    public function isTranslated()
    {
        return $this->getMessageTranslation() ? true : false;
    }

    abstract public function setCommentReceiverId($comment, $id);

    abstract public function getCommentReceiverId();

    abstract public function setCommentReceiver($comment, $receiver);

    abstract public function getCommentReceiver();
}
