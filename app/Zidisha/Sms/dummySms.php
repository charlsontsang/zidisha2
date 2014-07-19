<?php
namespace Zidisha\Sms;

use Zidisha\Mail\Mailer;

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

    public function send($phoneNumber, $text)
    {
        $data['to'] = $phoneNumber.'@test.com';
        $data['from'] = 'sms@zidisha.com';
        $data['subject'] = "SMS for " . $phoneNumber;
        $data['data'] = $text;

        $this->mailer->send('emails.sms.send-data', $data);
    }
}
