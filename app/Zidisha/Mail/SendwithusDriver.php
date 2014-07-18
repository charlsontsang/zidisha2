<?php
namespace Zidisha\Mail;

use sendwithus\API;

class SendwithusDriver implements MailDriverInterface
{
    protected $api;

    public function __construct()
    {
        $apiKey = \Setting::get('Sendwithus.apiKey');
        $this->api = new API($apiKey);
    }

    public function send($view, $data)
    {
        $to = $data['to'];
        unset($data['to']);

        $from = $data['from'];
        unset($data['from']);

        $replyTo = $data['replyTo'];
        unset($data['replyTo']);

        $templateId = $data['templateId'];
        unset($data['templateId']);

        $response = $this->api->send(
            $templateId,
            [
                'address' => $to
            ],
            [
                'email_data' => $data,
                'sender'     => [
                    'name'     => 'Zidisha',
                    'address'  => $from,
                    'reply_to' => $replyTo,
                ]
            ]
        );
    }
}