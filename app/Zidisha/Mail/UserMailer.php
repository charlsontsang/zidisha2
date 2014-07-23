<?php

namespace Zidisha\Mail;

use Zidisha\Comment\Comment;
use Zidisha\User\User;

class UserMailer
{

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sentLendingGroupCommentNotification(Comment $comment, User $user)
    {

    }
}
