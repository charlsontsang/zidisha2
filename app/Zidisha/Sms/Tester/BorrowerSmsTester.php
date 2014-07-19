<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Profile;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Sms\dummySms;
use Zidisha\Sms\SmsService;

class BorrowerSmsTester {

    private $borrowerSmsService;

    public function __construct(BorrowerSmsService $sms)
    {
        $this->borrowerSmsService = $sms;
    }

    public function sendBorrowerJoinedContactConfirmationSms()
    {
        $profile = new Profile();
        $profile->setPhoneNumber('234234');

        $borrower = new Borrower();
        $borrower->setFirstName('First Name');
        $borrower->setLastName('Last Name');
        $borrower->setProfile($profile);

        $contact = new Contact();
        $contact->setBorrower($borrower);
        $contact->setPhoneNumber('123123123');

        $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
    }
}
