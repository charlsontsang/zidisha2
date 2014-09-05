<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\JoinLog;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\Installment;
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

    public function sendDisbursedLoanMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        $loan->setDisbursedAt(new \DateTime());
        $loan->setDisbursedAmount(Money::create('300', $loan->getCurrencyCode()));

        $this->borrowerMailer->sendDisbursedLoanMail($loan);
    }

    public function sendLoanFinalArrearMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $loan = LoanQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $loan->getCurrencyCode()))
            ->setLoan($loan)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFinalArrearMail($borrower, $loan, $installment);
    }

    public function sendLoanFirstArrearMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $loan = LoanQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $loan->getCurrencyCode()))
            ->setLoan($loan)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFirstArrearMail($borrower, $loan, $installment);
    }

    public function sendLoanMonthlyArrearMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();

        $this->borrowerMailer->sendLoanMonthlyArrearMail($borrower);
    }

    public function sendRepaymentReminderTomorrow()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setPaidAmount(Money::create(135, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendRepaymentReminderTomorrow($borrower, $installment);
    }

    public function sendRepaymentReminder()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendRepaymentReminder($borrower,$installment);
    }
} 
