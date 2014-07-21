<?php

namespace Zidisha\Mail;

use Zidisha\Comment\Comment;

class AdminMailer
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendBorrowerCommentNotification(Comment $comment)
    {
        //$admin = UserQuery::create()
        //    ->findOneById(\Settings::get('site.adminId'));
    }
}
