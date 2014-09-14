<?php

namespace Zidisha\Vendor\SiftScience;

use SiftClient;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Contact;
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
    const NEW_ACCOUNT_TYPE_CREATE = '$create_account';
    const NEW_ACCOUNT_TYPE_EDIT = '$update_account';

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
                'amount'   => $amount->getAmount(),
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

    public function sendNewBorrowerAccountEvent(Borrower $borrower, $type)
    {
        $user = $borrower->getUser();
        $profile = $borrower->getProfile();
        $familyMembers = $borrower->getFamilyMembers();
        $neighbors = $borrower->getNeighbors();
        $i = 1;
        $data = [];
        /** @var Contact $familyMember */
        foreach ($familyMembers as $familyMember) {
            $data['family_contact_' . $i] = $familyMember->getName() . ' (' . $familyMember->getPhoneNumber() . ')';
        }
        for ($j = 1; $j <= (3 - count($familyMembers)); $j++) {
            $data['family_contact_' . (count($familyMembers) + $j)] = '';
        }
        $i = 1;
        /** @var Contact $neighbor */
        foreach ($neighbors as $neighbor) {
            $data['neighbor_contact_' . $i] = $neighbor->getName() . ' (' . $neighbor->getPhoneNumber() . ')';
        }
        for ($j = 1; $j <= (3 - count($neighbors)); $j++) {
            $data['neighbor_contact_' . (count($neighbors) + $j)] = '';
        }
        $communityLeader = $borrower->getCommunityLeader();
        $this->sift->track(
            $type,
            $data + [
                '$user_id'               => $user->getId(),
                '$session_id'            => $this->sessionId,
                'username'               => $user->getUsername(),
                'first_name'             => $borrower->getFirstName(),
                'last_name'              => $borrower->getLastName(),
                '$billing_address'       => array(
                    'address'  => $profile->getAddress(),
                    '$city'    => $profile->getCity(),
                    '$country' => $borrower->getCountry()->getName()
                ),
                'national_id'            => $profile->getNationalIdNumber(),
                '$user_email'            => $user->getEmail(),
                '$phone'                 => $profile->getPhoneNumber(),
                'community_leader'       => $communityLeader->getName(),
                'community_leader_phone' => $communityLeader->getPhoneNumber(),
                'about_me'               => $profile->getAboutMe(),
                'about_business'         => $profile->getAboutBusiness(),
                'hear_about_zidisha'     => '', //TODO it's for reffered_by column in old DB
                '$time'                  => time(),
            ]
        );
    }

    //TODO usage
    public function sendOnTimePaymentLabel(Borrower $borrower)
    {
        $userId = $borrower->getId();

        $this->sift->label(
            $userId,
            [
                '$is_bad'      => false,
                '$type'        => 'ontime_payment',
                '$user_id'     => $userId,
                'reasons'      => 'High on-time repayment rate',
                '$description' => 'Made payment and historic on-time repayment rate is high',
                '$time'        => time()
            ]
        );
    }
}
