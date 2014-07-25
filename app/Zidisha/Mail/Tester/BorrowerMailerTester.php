<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\JoinLog;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\User\User;

class BorrowerMailerTester
{
    /**
     * @var \Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;

    public function __construct(BorrowerMailer $borrowerMailer)
    {
        $this->borrowerMailer = $borrowerMailer;
    }

    public function sendVerificationMail()
    {
        $user = new User();
        $user->setEmail('testuser@email.com');

        $joinLog = new JoinLog();
        $joinLog->setVerificationCode('test-verification-code');

        $borrower = new Borrower();
        $borrower->setUser($user);
        $borrower->setJoinLog($joinLog);

        $this->borrowerMailer->sendVerificationMail($borrower);
    }

    public function sendBorrowerJoinedConfirmationMail()
    {
        $user = new User();
        $user->setEmail('test@test.com');

        $borrower = new Borrower();
        $borrower->setUser($user);

        $this->borrowerMailer->sendBorrowerJoinedConfirmationMail($borrower);
    }


    public function sendExpiredLoanMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        
        $this->borrowerMailer->sendExpiredLoanMail($loan);
    }
} 
