<?php
namespace Zidisha\Sms;

use Zidisha\Mail\Mailer;
use Zidisha\Utility\Utility;

class dummySms
{
    /**
     * @var \Zidisha\Mail\Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($phoneNumber, $data)
    {
        $number =  Utility::formatNumber($phoneNumber, $data['countryCode']);
        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         =>  $number.'@test.com',
                'subject'    => "SMS for " . $number
            ]
        );
    }
}
