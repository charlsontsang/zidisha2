<?php
namespace Zidisha\Mail;

use Config;
use Mail;

class Mailer
{

    public $driver;

    public function __construct()
    {
        $this->driver = Config::get('mail.mailer.driver');
    }

    public function send($data)
    {
        if ($this->driver == 'laravel') {
            Mail::send(
                'emails.master',
                $data,
                function ($message) use ($data) {
                    $message
                        ->to($data['to'])
                        ->from($data['from'])
                        ->subject($data['subject']);
                }
            );
        }
    }

    public function queue($data)
    {
        if ($this->driver == 'laravel') {
            Mail::queue(
                'emails.master',
                $data,
                function ($message) use ($data) {
                    $message
                        ->to($data['to'])
                        ->from($data['from'])
                        ->subject($data['subject']);
                }
            );
        }
    }
}
