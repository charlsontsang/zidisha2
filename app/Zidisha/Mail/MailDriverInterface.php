<?php
namespace Zidisha\Mail;

interface MailDriverInterface
{
    public function send($view, $data);
}
