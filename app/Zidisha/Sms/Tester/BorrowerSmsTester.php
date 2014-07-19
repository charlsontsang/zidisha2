<?php
namespace Zidisha\Sms\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Profile;
use Zidisha\Sms\dummySms;

class BorrowerSmsTester {

    /**
     * @var \Zidisha\Sms\dummySms
     */
    private $sms;

    public function __construct(dummySms $sms)
    {
        $this->sms = $sms;
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

        $arguments = [
            'borrowerName'        => $contact->getBorrower()->getName(),
            'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
            'contactName'         => $contact->getName(),
        ];

        $text = \Lang::get('borrower.join.sms.contact-confirmation', $arguments);
        $this->sms->send($contact->getPhoneNumber(), $text);
    }
}
