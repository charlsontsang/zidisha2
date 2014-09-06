<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Borrower\JoinLog;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Borrower\VolunteerMentorQuery;
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
        $borrower = BorrowerQuery::create()
            ->findOne();

        $this->borrowerMailer->sendBorrowerJoinedConfirmationMail($borrower);
    }

    public function sendFormResumeLaterMail()
    {
        $this->borrowerMailer->sendFormResumeLaterMail('jdjdjdj@gg.com', '12345abcde');
    }

    public function sendBorrowerJoinedVolunteerMentorConfirmationMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $volunteerMentor = VolunteerMentorQuery::create()
            ->findOne();
        $borrower->setVolunteerMentor($volunteerMentor);

        $this->borrowerMailer->sendBorrowerJoinedVolunteerMentorConfirmationMail($borrower);
    }

    public function sendExpiredLoanMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        
        $this->borrowerMailer->sendExpiredLoanMail($borrower);
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

    public function sendApprovedConfirmationMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $this->borrowerMailer->sendApprovedConfirmationMail($borrower);
    }

    public function sendDeclinedConfirmationMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $this->borrowerMailer->sendDeclinedConfirmationMail($borrower);
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
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFinalArrearMail($borrower, $installment);
    }

    public function sendLoanFirstArrearMail()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFirstArrearMail($borrower, $installment);
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

    public function sendRepaymentReminderForDueAmount()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $dueAmount = Money::create(60, $borrower->getCountry()->getCurrencyCode());

        $this->borrowerMailer->sendRepaymentReminderForDueAmount($borrower, $installment, $dueAmount);
    }

    public function sendAgainRepaymentReminder()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $dueAmount = Money::create(60, $borrower->getCountry()->getCurrencyCode());

        $this->borrowerMailer->sendAgainRepaymentReminder($borrower, $installment, $dueAmount);
    }

    public function sendBorrowerInvite()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $invite = InviteQuery::create()
            ->findOne();

        $this->borrowerMailer->sendBorrowerInvite($borrower, $invite, 'join zidisha dude', 'hey...........join here');
    }

    public function sendLoanMonthlyArrearToVolunteerMentor()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $vm = VolunteerMentorQuery::create()
            ->findOne();

        $this->borrowerMailer->sendLoanMonthlyArrearToVolunteerMentor($vm, $borrower, $installment);
    }
} 
