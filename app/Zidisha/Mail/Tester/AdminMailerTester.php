<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\AdminMailer;
use Zidisha\User\UserQuery;

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
        $loan = LoanQuery::create()
            ->findOne();
        $comment = BorrowerCommentQuery::create()
            ->findOne();
        $postedBy = 'dmdm by hddhd on ffjfjfjf';
        $images = '.....';

        $this->adminMailer->sendBorrowerCommentNotification($loan, $comment, $postedBy, $images);
    }

    public function sendErrorMail()
    {
        $this->adminMailer->sendErrorMail(new \Exception('This is the error message.'));    
    }
}