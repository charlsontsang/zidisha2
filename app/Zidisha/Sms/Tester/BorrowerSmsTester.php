<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Profile;
use Zidisha\Currency\Money;
use Zidisha\Loan\LoanQuery;
use Zidisha\Repayment\Installment;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Sms\dummySms;
use Zidisha\Sms\SmsService;

class BorrowerSmsTester {

    private $borrowerSmsService;

    public function __construct(BorrowerSmsService $borrowerSmsService)
    {
        $this->borrowerSmsService = $borrowerSmsService;
    }

    public function sendBorrowerJoinedContactConfirmationSms()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();

        $contact = new Contact();
        $contact->setBorrower($borrower);
        $contact->setPhoneNumber('123123123');

        $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
    }

    public function sendLoanFinalArrearNotification()
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

        $this->borrowerSmsService->sendLoanFinalArrearNotification($borrower, $loan, $installment);
    }

    public function sendLoanFirstArrearNotification()
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

        $this->borrowerSmsService->sendLoanFirstArrearNotification($borrower, $loan, $installment);
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
        $amounts = [
            'amount_total'      => 45,
            'paid_amount_total' => 20
        ];

        $this->borrowerSmsService->sendRepaymentReminderForDueAmount($borrower, $installment, $amounts);
    }
}
