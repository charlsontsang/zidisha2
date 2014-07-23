<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Comment\LendingGroupCommentQuery;
use Zidisha\Mail\UserMailer;
use Zidisha\User\UserQuery;

class UserMailerTester
{
    /**
     * @var \Zidisha\Mail\UserMailer
     */
    private $userMailer;

    public function __construct(UserMailer $userMailer)
    {
        $this->userMailer = $userMailer;
    }

    public function sentLendingGroupCommentNotification()
    {
        $comment = LendingGroupCommentQuery::create()
            ->findOne();

        $user = UserQuery::create()
            ->findOne();

        $this->userMailer->sentLendingGroupCommentNotification($comment, $user);
    }
} 
