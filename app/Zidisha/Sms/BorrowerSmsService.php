<?php

namespace Zidisha\Sms;


use Zidisha\Borrower\ContactQuery;

class BorrowerSmsService {

    private $smsService;

    public function __construct(SmsService $smsService){

        $this->smsService = $smsService;
    }

    public function sendContactConfirmationSms($borrower)
    {
        $contacts = ContactQuery::create()
            ->filterByBorrower($borrower)
            ->find();
        $data = [];

        foreach($contacts as $contact){
            $data['phoneNumber'] = $contact->getPhoneNumber();
            $data['contact'] = $contact;
            $this->smsService->send('emails.sms', $data);
        }
    }

} 