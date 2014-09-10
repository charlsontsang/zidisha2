<?php

namespace Zidisha\Vendor\SiftScience\Tester;

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Contact;
use Zidisha\Sms\BorrowerSmsService;

class SiftScienceTester {

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
