<?php

namespace Zidisha\Vendor\SiftScience;

use Zidisha\Borrower\Borrower;
use Zidisha\User\User;

class SiftScienceService
{
    protected $siftScienceKey;

    protected $siftScienceUrl;

    protected $sessionId;

    public function __construct()
    {
        $this->siftScienceKey = \Config::get('siftscience.api_key');
        $this->siftScienceUrl = "https://api.siftscience.com/v203/events";
        $this->sessionId = \Session::getId();
    }

    public function sendLoginEvent(User $user)
    {
        $data = [
            '$type'         => '$login',
            '$api_key'      => $this->siftScienceKey,
            '$user_id'      => $user->getId(),
            '$session_id'   => $this->sessionId,
            '$login_status' => '$success',
            '$time'         => time()
        ];

        $this->sendEvent($this->siftScienceUrl, json_encode($data));
    }

    public function sendInvalidLoginEvent()
    {
        $data = array(
            '$type'         => '$login',
            '$api_key'      => $this->siftScienceKey,
            '$session_id'   => $this->sessionId,
            '$login_status' => '$failure',
            '$time'         => time()
        );

        $this->sendEvent($this->siftScienceUrl, json_encode($data));
    }

    public function sendLogoutEvent(User $user)
    {
        $data = array(
            '$type'       => '$logout',
            '$api_key'    => $this->siftScienceKey,
            '$user_id'    => $user->getId(),
            '$session_id' => $this->sessionId
        );

        $this->sendEvent($this->siftScienceUrl, json_encode($data));
    }

    public function sendBorrowerDeclinedEvent(Borrower $borrower)
    {
        $userId = $borrower->getId();

        $data = array(
            '$type'        => 'decline',
            '$api_key'     => $this->siftScienceKey,
            '$user_id'     => $userId,
            '$is_bad'      => true,
            'reasons'      => 'Declined',
            '$description' => 'Borrower application declined',
            '$time'        => time()
        );

        $siftScienceUrl = "https://api.siftscience.com/v203/users/" . $userId . "/labels";
        $this->sendEvent($siftScienceUrl, json_encode($data));
    }

    protected function sendEvent($url, $post)
    {
        if (\App::environment() == 'local') {
            \Mail::send(
                'emails.siftScience.sendData',
                ['siftData' => $post],
                function ($mail) {
                    $mail
                        ->to('siftScience@gmail.com')
                        ->from('siftScience@zidisha.com')
                        ->subject("siftScienceEvent");
                }
            );
        }
    }
}
