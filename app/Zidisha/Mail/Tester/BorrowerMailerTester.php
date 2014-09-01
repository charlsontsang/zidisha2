<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\JoinLog;
use Zidisha\Loan\Loan;
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

    public function sendLoanConfirmationMail()
    {
        $user = new User();
        $user->setEmail('test@test.com');

        $borrower = new Borrower();
        $borrower->setUser($user);
        $borrower->setFirstName('First Name');
        $borrower->setLastName('Last Name');
        
        $loan = new Loan();
        $loan->setId(14);
        
        
        $this->borrowerMailer->sendLoanConfirmationMail($borrower, $loan);
    }

    public function sendLoanFullyFundedMail()
    {
        $user = new User();
        $user->setEmail('test@test.com');

        $borrower = new Borrower();
        $borrower->setUser($user);
        $borrower->setFirstName('First Name');
        $borrower->setLastName('Last Name');

        $loan = new Loan();
        $loan->setId(14);
        $loan->setAppliedAt(new \DateTime());
        $loan->setBorrower($borrower);
        $borrower->setActiveLoan($loan);

        $this->borrowerMailer->sendLoanFullyFundedMail($loan);
    }
} 
