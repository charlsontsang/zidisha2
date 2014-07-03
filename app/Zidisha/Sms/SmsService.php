<?php

namespace Zidisha\Sms;


class SmsService {
    
    public function send($view, $data)
    {
        \Mail::send(
            $view,
            $data,
            function ($mail) use ($data) {
                $mail
                    ->to('sms@zidisha.com', $data['phoneNumber'])
                    ->from('sms@zidisha.com')
                    ->subject("SMS for " . $data['phoneNumber']);
            }
        );
    }

    public function queue($view, $data)
    {
        \Mail::queue(
            $view,
            $data,
            function ($mail) use ($data) {
                $mail
                    ->to('sms@zidisha.com', $data['phoneNumber'])
                    ->from('sms@zidisha.com')
                    ->subject("SMS for " . $data['phoneNumber']);
            }
        );
    }
}
