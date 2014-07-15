<?php
namespace Zidisha\Vendor\SiftScience;

class dummySiftScience
{
    public function track($event, $properties)
    {
        \Mail::send(
            'emails.siftScience.sendData',
            ['siftData' => $properties, 'type' => 'track'],
            function ($mail) {
                $mail
                    ->to('siftScience@gmail.com')
                    ->from('siftScience@zidisha.com')
                    ->subject('sift-Science track event');
            }
        );
    }

    public function label($userId, $properties)
    {
        \Mail::send(
            'emails.siftScience.sendData',
            ['siftData' => $properties, 'type' => 'label'],
            function ($mail) {
                $mail
                    ->to('siftScience@gmail.com')
                    ->from('siftScience@zidisha.com')
                    ->subject('sift-Science label user');
            }
        );
    }

    public function score($userId)
    {
        \Mail::send(
            'emails.siftScience.sendData',
            ['siftData' => 'NOT APPLICABLE', 'type' => 'score'],
            function ($mail) {
                $mail
                    ->to('siftScience@gmail.com')
                    ->from('siftScience@zidisha.com')
                    ->subject('sift-Science user score');
            }
        );

        $response = new dummySiftResponse();
        $response->setBody([ 'score' => '0.03030' ]);

        return $response ;
    }
} 