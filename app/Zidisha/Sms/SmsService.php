<?php
namespace Zidisha\Sms;

use Zidisha\Utility\Utility;

class SmsService
{

    /**
     * @var Telerivet
     */
    private $sms;

    public function __construct(dummySms $dummySms, Telerivet $sms)
    {
        if (\Config::get('services.sms.enabled')) {
            $this->sms = $sms;
        } else {
            $this->sms = $dummySms;
        }
    }

    public function send($phoneNumber, $text, $countryCode)
    {
        $number = Utility::formatNumber($phoneNumber, $countryCode);
        $this->sms->send($number, $text);
    }

    public function queue($phoneNumber, $text, $queue = null)
    {
        \Queue::push('Zidisha\Sms\SmsService@handleQueuedMessage', compact('phoneNumber', 'text', 'countryCode'), $queue);
    }

    public function later($delay, $phoneNumber, $text, $queue = null)
    {
        \Queue::later($delay, 'Zidisha\Sms\SmsService@handleQueuedMessage', compact('phoneNumber', 'text', 'countryCode'), $queue);
    }

    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['phoneNumber'], $data['text'], $data['countryCode']);

        $job->delete();
    }
}
