<?php
namespace Zidisha\Mail;

use Config;
use Mail;
use Zidisha\Admin\Setting;

class Mailer
{

    public $driver;

    protected $enabled;

    public function __construct()
    {
        $this->enabled = \Config::get('mail.mailer.enabled');
        $this->driver = \Config::get('mail.mailer.driver');
    }

    public function send($view, $data)
    {
        $data += [
            'from' => Setting::get('site.replyTo'),
        ];
        if ($this->driver == 'laravel' && $this->enabled) {
            \Mail::send(
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
        $data += [
            'from' => Setting::get('site.replyTo'),
        ];
        if ($this->driver == 'laravel' && $this->enabled) {
            \Mail::queue(
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

    public function later($delay, $view, $data)
    {
        $data += [
            'from' => Setting::get('site.replyTo'),
        ];
        if ($this->driver == 'laravel' && $this->enabled) {
            \Mail::later(
                $delay,
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
