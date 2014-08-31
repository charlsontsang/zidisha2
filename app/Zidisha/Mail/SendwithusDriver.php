<?php
namespace Zidisha\Mail;

use sendwithus\API;

class SendwithusDriver implements MailDriverInterface
{
    protected $api;

    public function __construct()
    {
        $apiKey = \Setting::get('sendwithus.apiKey');
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

        $data = $data + [
                'footer'      => \Lang::get('lender.mails.sendwithus-defaults.footer'),
                'button_url'  => route('loan:index'),
                'button_text' => \Lang::get('lender.mails.sendwithus-defaults.button_text'),
            ];

        $data['button'] = [
            'url' => $data['button_url'],
            'text' => $data['button_text'],
        ];
        
        unset($data['button_url']);
        unset($data['button_text']);
        
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
