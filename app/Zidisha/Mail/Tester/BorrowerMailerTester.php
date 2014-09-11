<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Invite;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Borrower\JoinLog;
use Zidisha\Borrower\Profile;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Comment\CommentQuery;
use Zidisha\Country\CountryQuery;
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
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $this->borrowerMailer->sendBorrowerJoinedConfirmationMail($borrower);
    }

    public function sendFormResumeLaterMail()
    {
        $this->borrowerMailer->sendFormResumeLaterMail('jdjdjdj@gg.com', '12345abcde');
    }

    public function sendBorrowerJoinedVolunteerMentorConfirmationMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $userBorrowerVM = new User();
        $userBorrowerVM->setUsername('LenderTestvm')
            ->setEmail('lendertestvm@gmail.com');
        $borrowerVM = new Borrower();
        $borrowerVM->setUser($userBorrowerVM)
            ->setFirstName('borrowerFirstNamevm')
            ->setLastName('borrowerLastNamevm');
        $volunteerMentor = new VolunteerMentor();
        $volunteerMentor->setBorrowerVolunteer($borrowerVM);

        $borrower->setVolunteerMentor($volunteerMentor);

        $this->borrowerMailer->sendBorrowerJoinedVolunteerMentorConfirmationMail($borrower);
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
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $this->borrowerMailer->sendApprovedConfirmationMail($borrower);
    }

    public function sendDeclinedConfirmationMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $this->borrowerMailer->sendDeclinedConfirmationMail($borrower);
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

        $this->borrowerMailer->sendBorrowerCommentNotification($borrower, $loan, $comment, $postedBy, $images);
    }

    public function sendExpiredLoanMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $this->borrowerMailer->sendExpiredLoanMail($borrower);
    }

    public function sendBorrowerInvite()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $invite = new Invite();
        $invite->setHash('invitehash')
            ->setEmail('lenderinvite@mail.com');

        $this->borrowerMailer->sendBorrowerInvite($borrower, $invite, 'join zidisha dude', 'hey...........join here');
    }

    public function sendAgainRepaymentReminder()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, 'XOF'))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $dueAmount = Money::create(60, 'XOF');

        $this->borrowerMailer->sendAgainRepaymentReminder($borrower, $installment, $dueAmount);
    }

    public function sendRepaymentReminder()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendRepaymentReminder($borrower, $installment);
    }

    public function sendLoanFinalArrearMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFinalArrearMail($borrower, $installment);
    }

    public function sendLoanFirstArrearMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendLoanFirstArrearMail($borrower, $installment);
    }

    public function sendLoanMonthlyArrearToContact()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $profile = new Profile();
        $profile->setBorrower($borrower)
            ->setPhoneNumber(4567865445);
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $name = "hehehehe";
        $email = "yoyo@ff.com";

        $this->borrowerMailer->sendLoanMonthlyArrearToContact($name, $email, $borrower, $installment);
    }

    public function sendLoanMonthlyArrearMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );

        $this->borrowerMailer->sendLoanMonthlyArrearMail($borrower);
    }

    public function sendRepaymentReminderTomorrow()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, 'XOF'))
            ->setPaidAmount(Money::create(135, 'XOF'))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerMailer->sendRepaymentReminderTomorrow($borrower, $installment);
    }

    public function sendRepaymentReminderForDueAmount()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(320, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $dueAmount = $installment->getAmount()->divide(4);

        $this->borrowerMailer->sendRepaymentReminderForDueAmount($borrower, $installment, $dueAmount);
    }

    public function sendDisbursedLoanMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setUsdAmount(Money::create('400'))
            ->setDisbursedAt(new \DateTime())
            ->setDisbursedAmount(Money::create('300', $borrower->getCountry()->getCurrencyCode()))
            ->setCurrencyCode($borrower->getCountry()->getCurrencyCode());

        $this->borrowerMailer->sendDisbursedLoanMail($loan);
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

    public function sendRepaymentReceiptMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );

        $this->borrowerMailer->sendRepaymentReceiptMail($borrower, Money::create(360, $borrower->getCountry()->getCurrencyCode()));
    }
}
