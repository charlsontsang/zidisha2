<?php
namespace Zidisha\Mail;


class LaravelMailerDriver implements MailDriverInterface
{
    public function send($view, $data)
    {
        \Mail::send(
            $view,
            $data + compact('data'),
            function ($message) use ($data) {
                $message
                    ->to($data['to'])
                    ->from($data['from'])
                    ->subject($data['subject']);
            }
        );
    }
}
