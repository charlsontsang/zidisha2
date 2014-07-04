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

    public function send($view, $data)
    {
        if ($this->driver == 'laravel') {
            Mail::send(
                $view,
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

    public function queue($view, $data)
    {
        if ($this->driver == 'laravel') {
            Mail::queue(
                $view,
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

    public function later($time, $view, $data)
    {
        if ($this->driver == 'laravel') {
            Queue::later(
                $time,
                $view,
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
