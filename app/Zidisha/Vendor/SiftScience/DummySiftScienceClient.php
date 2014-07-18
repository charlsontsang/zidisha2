<?php
namespace Zidisha\Vendor\SiftScience;

use Zidisha\Mail\Mailer;

class DummySiftScienceClient
{
    /**
     * @var \Zidisha\Mail\Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function track($event, $properties)
    {
        $this->mailer->send(
            'emails.siftScience.sendData',
            ['to' => 'siftscience@test.com', 'siftData' => $properties, 'type' => 'track', 'subject' => 'sift-Science track event']
        );
    }

    public function label($userId, $properties)
    {
        $this->mailer->send(
            'emails.siftScience.sendData',
            ['to' => 'siftscience@test.com', 'siftData' => $properties, 'type' => 'label', 'subject' => 'sift-Science label user']
        );
    }

    public function score($userId)
    {
        $this->mailer->send(
            'emails.siftScience.sendData',
            ['to' => 'siftscience@test.com', 'siftData' => 'NOT APPLICABLE', 'type' => 'score', 'subject' => 'sift-Science label user']
        );

        $response = new DummySiftScienceResponse();
        $response->setBody([ 'score' => mt_rand() / mt_getrandmax() ]);

        return $response ;
    }
} 