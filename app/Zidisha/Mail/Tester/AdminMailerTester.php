<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Mail\AdminMailer;

class AdminMailerTester
{
    /**
     * @var \Zidisha\Mail\AdminMailer
     */
    private $adminMailer;

    public function __construct(AdminMailer $adminMailer)
    {
        $this->adminMailer = $adminMailer;
    }

    public function sendBorrowerCommentNotification()
    {
        $comment = BorrowerCommentQuery::create()
                    ->findOne();

        $this->adminMailer->sendBorrowerCommentNotification($comment);
    }

    public function sendLendingGroupCommentNotification()
    {
        $comment = BorrowerCommentQuery::create()
            ->findOne();

        $this->adminMailer->sendLendingGroupCommentNotification($comment);
    }
}