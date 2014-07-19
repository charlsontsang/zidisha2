<?php
namespace Zidisha\Sms;

use Telerivet_API;

class Sms
{
    protected $client;

    public function __construct()
    {
        $telerivet = new Telerivet_API(\Setting::get('telerivet.apiKey'));

        $this->client = $telerivet->initProjectById(\Setting::get('telerivet.projectId'));
    }

    public function send($phoneNumber, $text)
    {
        $this->client->sendMessage(
            [
                'to_number' => $phoneNumber,
                'content'   => $text
            ]
        );
    }

    public function queue($phoneNumber, $text, $queue = null)
    {
        $data = [];
        $data['phoneNumber'] = $phoneNumber;
        $data['text'] = $text;

        \Queue::push('Zidisha\Sms\Sms@handleQueuedMessage', compact('data'), $queue);
    }

    public function later($delay, $phoneNumber, $text, $queue = null)
    {
        $data = [];
        $data['phoneNumber'] = $phoneNumber;
        $data['text'] = $text;

        \Queue::later($delay, 'Zidisha\Sms\Sms@handleQueuedMessage', compact('data'), $queue);
    }

    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['phoneNumber'], $data['text']);

        $job->delete();
    }
}
