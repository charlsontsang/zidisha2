<?php

namespace Zidisha\Sms;


use Zidisha\Borrower\Contact;

class BorrowerSmsService {

    private $smsService;

    public function __construct(SmsService $smsService){

        $this->smsService = $smsService;
    }

    public function sendBorrowerJoinedContactConfirmationSms(Contact $contact)
    {
        $arguments = [
            'borrowerName'        => $contact->getBorrower()->getName(),
            'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
            'contactName'         => $contact->getName(),
        ];
        $text = \Lang::get('borrowerJoin.sms.contact-confirmation', $arguments);
        $this->smsService->send($contact->getPhoneNumber(), $text);
    }

}
