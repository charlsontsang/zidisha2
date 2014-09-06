<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Comment\CommentQuery;
use Zidisha\Comment\LendingGroupCommentQuery;
use Zidisha\Lender\LendingGroupQuery;
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
        $comment = BorrowerCommentQuery::create()
            ->findOne();
        $lendingGroup = LendingGroupQuery::create()
            ->findOne();

        $user = UserQuery::create()
            ->findOne();
        $images = 'No Images dude!! it\'s a group not pinterest';

        $this->userMailer->sentLendingGroupCommentNotification($lendingGroup, $comment, $user, $images);
    }
}
