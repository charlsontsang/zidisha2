<?php
namespace Zidisha\Sms;

require_once base_path() . '/vendor/telerivet/telerivet-php-client/telerivet.php';

use Telerivet_API;
use Zidisha\Utility\Utility;

class Telerivet
{
    protected $client;

    public function __construct()
    {
        $telerivet = new Telerivet_API(\Setting::get('telerivet.apiKey'));

        $this->client = $telerivet->initProjectById(\Setting::get('telerivet.projectId'));
    }

    public function send($phoneNumber, $data)
    {
        $this->client->sendMessage(
            [
                'to_number' => Utility::formatNumber($phoneNumber, $data['countryCode']),
                'content'   => \Lang::get($data['label'], $data)
            ]
        );
    }
}
