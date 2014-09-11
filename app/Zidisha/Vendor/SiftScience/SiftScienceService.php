<?php

namespace Zidisha\Vendor\SiftScience;

use SiftClient;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Invite;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\User\User;

class SiftScienceService
{
    protected $sift;

    protected $sessionId;

    const TYPE_REPAYMENT = 'repayment';
    const TYPE_DISBURSEMENT = 'disbursement';

    public function __construct(DummySiftScienceClient $dummySiftScienceClient)
    {
        if (\Config::get('services.sift-science.enabled')) {
            $this->sift = new SiftClient(\Setting::get('sift-science.api-key'));
        } else {
            $this->sift = $dummySiftScienceClient;
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

    public function sendBorrowerDeclinedLabel(Borrower $borrower)
    {
        $userId = $borrower->getId();

        $this->sift->label(
            $userId,
            [
                '$is_bad'      => true,
                '$type'        => 'decline',
                '$user_id'     => $userId,
                'reasons'      => 'Declined',
                '$description' => 'Borrower application declined',
                '$time'        => time()
            ]
        );
    }

    public function getSiftScore(User $user)
    {
        $response = $this->sift->score($user->getId());

        if ($response->isOk()) {
            return $response->body['score'];
        } else {
            throw new \Exception('sift api error');
        }
    }

    public function loanArrearLabel(User $user, $loanId)
    {
        $this->sift->label(
            $user->getId(),
            [
                '$type'        => 'delay_loan',
                '$user_id'     => $user->getId(),
                'loanId'       => $loanId,
                '$is_bad'      => true,
                'reasons'      => 'delinquent',
                '$description' => 'loan falls over two months past due'
            ]
        );
    }

    public function sendBorrowerCommentEvent(Comment $comment)
    {
        $this->sift->track(
            'comment_post',
            [
                '$user_id' => $comment->getUserId(),
                'comment'  => $comment->getMessage(),
                '$time'    => time()
            ]
        );
    }

    public function sendBorrowerInviteAcceptedEvent(Invite $invite)
    {
        $this->sift->track(
            'borrower_invite',
            [
                '$user_id'    => $invite->getInviteeId(),
                '$session_id' => $this->sessionId,
                'invited_by'  => $invite->getBorrowerId(),
                '$time'       => time()
            ]
        );
    }

    public function sendBorrowerPaymentEvent($eventType, Borrower $borrower, Money $amount)
    {
        $this->sift->track(
            $eventType,
            [
                '$user_id' => $borrower->getId(),
                'amount'   => $amount, //TODO , ok to send Money object?
                '$time'    => time()
            ]
        );
    }

    public function sendFacebookEvent(User $user, $facebookId)
    {
        $this->sift->track(
            'facebook_link',
            [
                '$user_id'    => $user->getId(),
                '$session_id' => $this->sessionId,
                'facebook_id' => $facebookId,
                '$time'       => time()
            ]
        );
    }
}
