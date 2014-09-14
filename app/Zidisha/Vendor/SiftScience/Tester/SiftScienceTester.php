<?php

namespace Zidisha\Vendor\SiftScience\Tester;

use Zidisha\Borrower\Base\BorrowerQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Invite;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\User\User;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class SiftScienceTester {

    private $siftScienceService;

    public function __construct(SiftScienceService $siftScienceService)
    {
        $this->siftScienceService = $siftScienceService;
    }

    public function loanArrearLabel()
    {
        $user = new User();
        $user->setId(5);
        $loanId = 10;

        $this->siftScienceService->loanArrearLabel($user, $loanId);
    }

    public function sendLoginEvent()
    {
        $user = new User();
        $user->setId(5);

        $this->siftScienceService->sendLoginEvent($user);
    }

    public function sendInvalidLoginEvent()
    {

        $this->siftScienceService->sendInvalidLoginEvent();
    }

    public function sendLogoutEvent()
    {
        $user = new User();
        $user->setId(5);

        $this->siftScienceService->sendLogoutEvent($user);
    }

    public function sendBorrowerDeclinedEvent()
    {
        $borrower = new Borrower();
        $borrower->setId(5);

        $this->siftScienceService->sendBorrowerDeclinedLabel($borrower);
    }

    public function sendBorrowerCommentEvent()
    {
        $comment = new BorrowerComment();
        $comment->setUserId(5)
            ->setMessage('HowDay, Comment!!!');

        $this->siftScienceService->sendBorrowerCommentEvent($comment);
    }

    public function sendBorrowerInviteAcceptedEvent()
    {
        $invite = new Invite();
        $invite->setInviteeId(10)
            ->setBorrowerId(5);

        $this->siftScienceService->sendBorrowerInviteAcceptedEvent($invite);
    }

    public function sendBorrowerPaymentEvent()
    {
        $eventType = SiftScienceService::TYPE_REPAYMENT;
        $borrower = new Borrower();
        $borrower->setId(5);
        $amount = Money::create(800, 'XOF');

        $this->siftScienceService->sendBorrowerPaymentEvent($eventType, $borrower, $amount);
    }

    public function sendFacebookEvent()
    {
        $user = new User();
        $user->setId(5);
        $facebookId = 33445566;

        $this->siftScienceService->sendFacebookEvent($user, $facebookId);
    }

    public function sendNewBorrowerAccountEvent()
    {
        $borrower = BorrowerQuery::create()
            ->findOne();

        $this->siftScienceService->sendNewBorrowerAccountEvent($borrower, SiftScienceService::NEW_ACCOUNT_TYPE_CREATE);
    }

    public function sendOnTimePaymentLabel()
    {
        $borrower = new Borrower();
        $borrower->setId(5)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');

        $this->siftScienceService->sendOnTimePaymentLabel($borrower);
    }
}