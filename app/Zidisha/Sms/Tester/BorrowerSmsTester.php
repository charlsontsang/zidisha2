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
}
