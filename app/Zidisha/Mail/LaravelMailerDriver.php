<?php
namespace Zidisha\Mail;


use Zidisha\Utility\Utility;

class LaravelMailerDriver implements MailDriverInterface
{
    public function send($view, $data)
    {
        \Mail::send(
            $view,
            $data + compact('data'),
            function ($message) use ($data) {
                $message
                    ->to(Utility::clearPost($data['to']))
                    ->from(Utility::clearPost($data['from']))
                    ->subject(stripcslashes(Utility::clearPost($data['subject'])));
            }
        );
    }
}
