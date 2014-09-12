<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Contact;
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

    public function __construct(BorrowerSmsService $borrowerSmsService)
    {
        $this->borrowerSmsService = $borrowerSmsService;
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
            ->setProfile($profile)
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );

        $contact = new Contact();
        $contact->setBorrower($borrower);
        $contact->setPhoneNumber('123123123');

        $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
    }

    public function sendLoanFinalArrearNotification()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendLoanFinalArrearNotification($borrower, $installment);
    }

    public function sendLoanFirstArrearNotification()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
        $installment = new Installment();
        $installment->setDueDate(new \DateTime())
            ->setAmount(Money::create(340, $borrower->getCountry()->getCurrencyCode()))
            ->setLoanId(5)
            ->setBorrower($borrower);

        $this->borrowerSmsService->sendLoanFirstArrearNotification($borrower, $installment);
    }

    public function sendLoanMonthlyArrearNotificationToContact()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();
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
        $borrower = BorrowerQuery::create()
            ->findOne();

        $this->borrowerSmsService->sendLoanMonthlyArrearNotification($borrower);
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

        $this->borrowerSmsService->sendRepaymentReminderTomorrow($borrower, $installment);
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

        $this->borrowerSmsService->sendRepaymentReminder($borrower, $installment);
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
            ->setProfile($profile)
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
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
            ->setProfile($profile)
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );

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
            ->setProfile($profile)
            ->setCountry(
                CountryQuery::create()
                    ->findOne()
            );
        $comment = new BorrowerComment();
        $comment->setMessage('this is comment for borrower!!');
        $postedBy = 'dmdm by hddhd on ffjfjfjf';

        $this->borrowerSmsService->sendBorrowerCommentNotificationSms($borrower, $comment, $postedBy);
    }
}
