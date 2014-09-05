<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Profile;
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
}
