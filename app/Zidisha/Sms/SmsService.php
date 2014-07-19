<?php
namespace Zidisha\Sms;

class SmsService
{

    /**
     * @var Sms
     */
    private $sms;

    public function __construct(dummySms $dummySms, Sms $sms)
    {
        if (\Config::get('services.sms.enabled')) {
            $this->sms = $sms;
        } else {
            $this->sms = $dummySms;
        }
    }

    public function send($phoneNumber, $text)
    {
        $this->sms->send($phoneNumber, $text);
    }

    public function queue($phoneNumber, $text)
    {
        $this->sms->queue($phoneNumber, $text);
    }

    public function later($phoneNumber, $text)
    {
        $this->sms->later('60*30', $phoneNumber, $text);
    }
}
