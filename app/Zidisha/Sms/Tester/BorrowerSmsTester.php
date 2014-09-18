<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Invite;
use Zidisha\Borrower\Profile;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Country\CountryQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\LoanQuery;
use Zidisha\Repayment\Installment;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Sms\dummySms;
use Zidisha\Sms\SmsService;
use Zidisha\User\User;

class BorrowerSmsTester
{

    private $borrowerSmsService;
    private $borrowerCountry;

    public function __construct(BorrowerSmsService $borrowerSmsService)
    {
        $this->borrowerSmsService = $borrowerSmsService;
        $this->borrowerCountry = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->findOne();
    }

    public function sendBorrowerJoinedContactConfirmationSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $contact = new Contact();
        $contact->setBorrower($borrower);
        $contact->setPhoneNumber('123123123');

        $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
    }

    public function sendLoanFinalArrearNotification()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendLoanFinalArrearNotification($borrower, $installment);
    }

    public function sendLoanFirstArrearNotification()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendLoanFirstArrearNotification($borrower, $installment);
    }

    public function sendLoanMonthlyArrearNotificationToContact()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $contact = new Contact();
        $contact->setBorrower($borrower);
        $contact->setFirstName("haha")
            ->setLastName("huhu");
        $contact->setPhoneNumber('123123123');

        $this->borrowerSmsService->sendLoanMonthlyArrearNotificationToContact($contact, $borrower, $installment);
    }

    public function sendLoanMonthlyArrearNotification()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $this->borrowerSmsService->sendLoanMonthlyArrearNotification($borrower);
    }

    public function sendRepaymentReminderTomorrow()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setPaidAmount(Money::create(135, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendRepaymentReminderTomorrow($borrower, $installment);
    }

    public function sendRepaymentReminder()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendRepaymentReminder($borrower, $installment);
    }

    public function sendRepaymentReminderForDueAmount()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);
        $dueAmount = Money::create(60, $borrower->getCountry()->getCurrencyCode());

        $this->borrowerSmsService->sendRepaymentReminderForDueAmount($borrower, $installment, $dueAmount);
    }

    public function sendRepaymentReceiptSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $amount = Money::create(250, $borrower->getCountry()->getCurrencyCode());

        $this->borrowerSmsService->sendRepaymentReceiptSms($borrower, $amount);
    }

    public function sendEligibleInviteSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $this->borrowerSmsService->sendEligibleInviteSms($borrower);
    }

    public function sendBorrowerCommentNotificationSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);

        $comment = new BorrowerComment();
        $comment->setMessage('this is comment for borrower!!');
        $postedBy = 'dmdm by hddhd on ffjfjfjf';

        $this->borrowerSmsService->sendBorrowerCommentNotificationSms($borrower, $comment, $postedBy);
    }

    public function sendInviteAlertSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('2345675434')
            ->setAlternatePhoneNumber('234523453');
        $borrower = new Borrower();
        $borrower
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName')
            ->setCountry($this->borrowerCountry)
            ->setProfile($profile);
        $profile2 = new Profile();
        $profile2->setPhoneNumber('22345675434')
            ->setAlternatePhoneNumber('2234523453');
        $borrower2 = new Borrower();
        $borrower2
            ->setFirstName('2borrowerFirstName')
            ->setLastName('2borrowerLastName')
            ->setProfile($profile2)
            ->setCountry($this->borrowerCountry);

        $invite = new Invite();
        $invite->setBorrower($borrower)
            ->setInvitee($borrower2);

        $this->borrowerSmsService->sendInviteAlertSms($invite);
    }
}
