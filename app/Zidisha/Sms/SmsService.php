<?php

namespace Zidisha\Sms;


class SmsService {
    
    public function send($phoneNumber, $text)
    {
        \Mail::send(
            'emails.sms',
            compact('text'),
            function ($mail) use ($phoneNumber) {
                $mail
                    ->to('sms@zidisha.com', $phoneNumber)
                    ->from('sms@zidisha.com')
                    ->subject("SMS for $phoneNumber");
            }
        );
    }

    public function queue($phoneNumber, $text)
    {
        \Mail::queue(
            'emails.sms',
            compact('text'),
            function ($mail) use ($phoneNumber) {
                $mail
                    ->to('sms@zidisha.com', $phoneNumber)
                    ->from('sms@zidisha.com')
                    ->subject("SMS for $phoneNumber");
            }
        );
    }
}
