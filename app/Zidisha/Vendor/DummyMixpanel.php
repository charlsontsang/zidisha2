<?php
namespace Zidisha\Vendor;


use Zidisha\Mail\Mailer;

class DummyMixpanel
{
    /**
     * @var \Zidisha\Mail\Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function identify($userID, $properties = array())
    {
        $data['to'] = 'mixpanel@mixpanel.com';
        $data['subject'] = 'identity called';
        $data['data'] = [
            'userId'     => $userID,
            'properties' => $properties
        ];

        $this->mailer->send('emails.mix-panel.send-data', $data);
    }

    public function alias($userID)
    {
        $data['to'] = 'mixpanel@mixpanel.com';
        $data['subject'] = 'alias called';
        $data['data'] = [
            'userId' => $userID
        ];

        $this->mailer->send('emails.mix-panel.send-data', $data);
    }

    public function track($eventName, $properties = array())
    {
        $data['to'] = 'mixpanel@mixpanel.com';
        $data['subject'] = 'track called';
        $data['data'] = [
            'eventName' => $eventName
        ];

        $this->mailer->send('emails.mix-panel.send-data', $data);
    }
}
