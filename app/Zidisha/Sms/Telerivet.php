<?php
namespace Zidisha\Sms;

require_once base_path() . '/vendor/telerivet/telerivet-php-client/telerivet.php';

use Telerivet_API;

class Telerivet
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
}
