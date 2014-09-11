<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Comment\CommentQuery;
use Zidisha\Comment\LendingGroupCommentQuery;
use Zidisha\Lender\LendingGroup;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Mail\UserMailer;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

class UserMailerTester
{
    private $userMailer;

    public function __construct(UserMailer $userMailer)
    {
        $this->userMailer = $userMailer;
    }

    public function sentLendingGroupCommentNotification()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $comment = new BorrowerComment();
        $comment->setMessage('comment message')
            ->setUser($userBorrower)
            ->setCreatedAt(new \DateTime());
        $lendingGroup = new LendingGroup();
        $lendingGroup->setName('test Group')
            ->setId(5);
        $user = new User();
        $user->setEmail('usertest@email.com');

        $images = 'No Images dude!! it\'s a group not pinterest';

        $this->userMailer->sentLendingGroupCommentNotification($lendingGroup, $comment, $user, $images);
    }
}
