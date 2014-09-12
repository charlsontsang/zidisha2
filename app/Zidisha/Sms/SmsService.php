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

    public function send($phoneNumber, $data)
    {
        $this->sms->send($phoneNumber, $data);
    }

    public function queue($phoneNumber, array $data, $queue = null)
    {
        \Queue::push('Zidisha\Sms\SmsService@handleQueuedMessage', compact('phoneNumber', 'data', 'countryCode'), $queue);
    }

    public function later($delay, $phoneNumber, array $data, $queue = null)
    {
        \Queue::later($delay, 'Zidisha\Sms\SmsService@handleQueuedMessage', compact('phoneNumber', 'data', 'countryCode'), $queue);
    }

    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['phoneNumber'], $data['data']);

        $job->delete();
    }
}
