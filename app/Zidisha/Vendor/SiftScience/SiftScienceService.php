<?php

namespace Zidisha\Vendor\SiftScience;

use SiftClient;
use Zidisha\Borrower\Borrower;
use Zidisha\User\User;

class SiftScienceService
{
    protected $sift;

    protected $sessionId;

    public function __construct()
    {
        if (\App::environment() == 'local') {
            $this->sift = new DummySiftScienceClient();
        } else {
            $this->sift = new SiftClient(\Setting::get('sift-science.api'));
        }

        $this->sessionId = \Session::getId();
    }

    public function sendLoginEvent(User $user)
    {
        $this->sift->track(
            '$login',
            [
                '$user_id'      => $user->getId(),
                '$session_id'   => $this->sessionId,
                '$login_status' => '$success',
                '$time'         => time()
            ]
        );
    }

    public function sendInvalidLoginEvent()
    {
        $this->sift->track(
            '$login',
            [
                '$session_id'   => $this->sessionId,
                '$login_status' => '$failure',
                '$time'         => time()
            ]
        );
    }

    public function sendLogoutEvent(User $user)
    {
        $this->sift->track(
            '$logout',
            [
                '$user_id'    => $user->getId(),
                '$session_id' => $this->sessionId
            ]
        );
    }

    public function sendBorrowerDeclinedEvent(Borrower $borrower)
    {
        $userId = $borrower->getId();

        $this->sift->label(
            $userId,
            array(
                '$is_bad'      => true,
                '$type'        => 'decline',
                '$user_id'     => $userId,
                '$reasons'     => 'Declined',
                '$description' => 'Borrower application declined',
                '$time'        => time()
            )
        );
    }

    public function getSiftScore(User $user)
    {
        $response = $this->sift->score($user->getId());

        if ($response->isOk()) {
            return $response->body['score']; // => 0.030301357270181357
        } else {
            throw new \Exception('sift api error');
        }
    }
}
