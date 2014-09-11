<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\AdminMailer;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

class AdminMailerTester
{
    private $adminMailer;

    public function __construct(AdminMailer $adminMailer)
    {
        $this->adminMailer = $adminMailer;
    }

    public function sendBorrowerCommentNotification()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $comment = new BorrowerComment();
        $comment->setMessage('this is comment for borrower!!');
        $postedBy = 'dmdm by hddhd on ffjfjfjf';
        $images = '.....';

        $this->adminMailer->sendBorrowerCommentNotification($loan, $comment, $postedBy, $images);
    }

    public function sendErrorMail()
    {
        $this->adminMailer->sendErrorMail(new \Exception('This is the error message.'));    
    }
}