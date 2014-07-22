<?php
namespace Zidisha\Lender;

use Carbon\Carbon;
use Propel\Runtime\Propel;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Loan;
use Zidisha\Mail\LenderMailer;
use Zidisha\Notification\Notification;
use Zidisha\Notification\NotificationQuery;
use Zidisha\Upload\Upload;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\PropelDB;

class LenderService
{

    private $lenderMailer;
    private $mixpanelService;
    private $userQuery;
    private $transactionService;

    public function __construct(
        LenderMailer $lenderMailer,
        MixpanelService $mixpanelService,
        UserQuery $userQuery,
        TransactionService $transactionService
    ) {
        $this->lenderMailer = $lenderMailer;
        $this->mixpanelService = $mixpanelService;
        $this->userQuery = $userQuery;
        $this->transactionService = $transactionService;
    }

    public function editProfile(Lender $lender, $data)
    {
        $lender->setFirstName($data['firstName']);
        $lender->setLastName($data['lastName']);
        $lender->getUser()->setEmail($data['email']);
        $lender->getUser()->setUsername($data['username']);
        $lender->getProfile()->setAboutMe($data['aboutMe']);

        if (!empty($data['password'])) {
            $lender->getUser()->setPassword($data['password']);
        }

        $lender->save();
    }

    public function uploadPicture(Lender $lender, $image)
    {
        $user = $lender->getUser();

        if ($image) {
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);
            $user->setProfilePicture($upload);
            $user->save();
        }
    }

    public function lenderInviteViaEmail($lender, $email, $subject, $custom_message)
    {
        $lender_invite = new Invite();
        $lender_invite->setLender($lender);
        $lender_invite->setEmail($email);
        $lender_invite->isInvited(true);
        $success = $lender_invite->save();

        if ($success) {
            $this->lenderMailer->sendLenderInvite($lender, $lender_invite, $subject, $custom_message);
        }

        return $lender_invite;
    }

    public function addLenderInviteVisit(Lender $lender, $shareType, Invite $invite = null)
    {
        $inviteVisit = new InviteVisit();
        $inviteVisit->setLender($lender);
        $inviteVisit->setInvite($invite);
        $inviteVisit->setShareType($shareType);
        $inviteVisit->setIpAddress(\Request::getClientIp());
        $inviteVisit->setHttpReferer(array_get($_SERVER, 'HTTP_REFERER', ""));
        $inviteVisit->save();

        $this->mixpanelService->trackInvitePage($lender, $inviteVisit, $shareType);

        return $inviteVisit;
    }


    function processLenderInvite(Lender $invitee, InviteVisit $lenderInviteVisit)
    {
        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        for ($retry = 0; $retry < 3; $retry++) {
            $con->beginTransaction();
            try {
                $invite = $lenderInviteVisit->getInvite();
                if ($invite) {
                    $res1 = $invite->setInvitee($invitee)->save();
                } else {
                    $invite = new Invite();
                    $invite->setLender($lenderInviteVisit->getLender());
                    $invite->setEmail($invitee->getUser()->getEmail());
                    $invite->setInvitee($invitee);
                    $invite->setInvited(false);
                    $res1 = $invitee->save($con);
                }
                if (!$res1) {
                    throw new \Exception();
                }
                $this->transactionService->addLenderInviteTransaction($con, $invite);
            } catch (\Exception $e) {
                $con->rollback();
            }
            $con->commit();

            //TODO , invite_notify(see below commented if statement)
            //   if ($lender['invite_notify']) {
            $this->lenderMailer->sendLenderInviteCredit($invite);
            // }
            $this->mixpanelService->trackInviteAccept($invite);
            return $invite;
        }

        return false;
    }

    public function notifyAbandonedLenders()
    {
        $c = new Carbon();
        $lastYear = $c->subYear();

        $abandonedLenders = LenderQuery::create()
            ->useUserQuery()
                ->filterAbandoned($lastYear)
            ->endUse()
            ->find();

        foreach ($abandonedLenders as $lender) {
            $this->lenderMailer->sendAbandonedMail($lender);
            $notification = new Notification();
            $notification->setType("abandoned")
                ->setUser($lender->getUser());
            $notification->save();
        }
    }

    public function deactivateAbandonedLenders()
    {
        $thirteenMonthsAgo = new Carbon();
        $thirteenMonthsAgo->subMonths(13);
        $oneMonthAgo = new Carbon();
        $oneMonthAgo->subMonth();

        $lenders = LenderQuery::create()
            ->useUserQuery()
                ->filterAbandoned($thirteenMonthsAgo)
                ->useNotificationQuery()
                    ->filterByType("abandoned")
                    ->filterByCreatedAt(['max' => $oneMonthAgo])
                ->endUse()
            ->endUse()
            ->find();

        foreach($lenders as $lender) {
            $this->deactivateLender($lender);
        }
    }

    public function deactivateLender(Lender $lender)
    {
        if (!$lender->isActive()) {
            return false;
        }
        $currentBalance = TransactionQuery::create()
            ->filterByUser($lender->getUser())
            ->getTotalAmount();

        if ($currentBalance->isPositive()) {
            PropelDB::transaction(function($con) use ($lender, $currentBalance) {
                $this->transactionService->addConvertToDonationTransaction($con, $lender, $currentBalance);
                $lender
                    ->setAdminDonate(true)
                    ->setActive(false);
                $lender->save($con);
                $lender->getUser()->setActive(false);
                $lender->getUser()->save($con);
            });
        }

        return true;
    }

    public function joinLender($data)
    {
        $data += [
            'googleId'      => null,
            'googlePicture' => null,
            'firstName'     => null,
            'lastName'      => null,
            'aboutMe'       => null,
            'facebookId'    => null,
            'password'      => null,
        ];

        $user = new User();
        $user
            ->setPassword($data['password'])
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setRole('lender')
            ->setGoogleId($data['googleId'])
            ->setFacebookId($data['facebookId'])
            ->setGooglePicture($data['googlePicture']);

        $lender = new Lender();
        $lender
            ->setUser($user)
            ->setCountryId($data['countryId'])
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName']);

        $profile = new Profile();
        $profile->setAboutMe($data['aboutMe']);
        $lender->setProfile($profile);

        $preferences = new Preferences();
        $lender->setPreferences($preferences);
        
        $lender->save();

        $this->mixpanelService->trackLenderJoined($lender);
        
        $this->lenderMailer->sendWelcomeMail($lender);
        $this->lenderMailer->sendIntroductionMail($lender);

        return $lender;
    }

    public function joinFacebookUser($facebookUser, $data)
    {
        $data += [
            'email'      => $facebookUser['email'],
            'facebookId' => $facebookUser['id'],
            'firstName'  => $facebookUser['first_name'],
            'lastName'   => $facebookUser['last_name'],
        ];

        return $this->joinLender($data);
    }

    public function validateConnectingFacebookUser($facebookUser)
    {
        $checkUser = $this->userQuery
            ->filterByFacebookId($facebookUser['id'])
            ->_or()
            ->filterByEmail($facebookUser['email'])
            ->findOne();

        $errors = array();
        if ($checkUser) {
            if ($checkUser->getFacebookId() == $facebookUser['id']) {
                $errors[] = 'lender.join.validation.facebook-account-exists';
            } else {
                $errors[] = 'lender.join.validation.facebook-email-exists';
            }
        }

        return $errors;
    }

    public function joinGoogleUser(\Google_Service_Oauth2_Userinfoplus $googleUser, $data)
    {
        $data += [
            'email'         => $googleUser->getEmail(),
            'googleId'      => $googleUser->getId(),
            'googlePicture' => $googleUser->getPicture(),
            'firstName'     => $googleUser->getGivenName(),
            'lastName'      => $googleUser->getFamilyName()
        ];

        return $this->joinLender($data);
    }

    public function validateConnectingGoogleUser(\Google_Service_Oauth2_Userinfoplus $googleUser)
    {
        $checkUser = $this->userQuery
            ->filterByGoogleId($googleUser->getId())
            ->_or()
            ->filterByEmail($googleUser->getEmail())
            ->findOne();

        $errors = array();
        if ($checkUser) {
            if ($checkUser->getGoogleId() == $googleUser->getId()) {
                $errors[] = 'lender.join.validation.google-account-exists';
            } else {
                $errors[] = 'lender.join.validation.google-email-exists';
            }
        }

        return $errors;
    }

    // TODO Move to BalanceService
    public function addWithdrawRequest(Lender $lender, $data)
    {
        $amount = Money::create($data['withdrawAmount']);
        $withdrawalRequest = new WithdrawalRequest();
        $withdrawalRequest->setLender($lender)
            ->setAmount($amount)
            ->setPaypalEmail($data['paypalEmail']);

        PropelDB::transaction(function($con) use ($lender, $amount, $withdrawalRequest) {
                $this->transactionService->addWithdrawFundTransaction($con, $amount, $lender);
                $withdrawalRequest->save($con);
            });
        return $withdrawalRequest;
    }

    public function updateAccountPreferences(Lender $lender, $data)
    {
            $lender->getPreferences()
                ->setHideLendingActivity($data['hideLendingActivity'])
                ->setHideKarma($data['hideKarma'])
                ->setNotifyLoanFullyFunded($data['notifyLoanFullyFunded'])
                ->setNotifyLoanAboutToExpire($data['notifyLoanAboutToExpire'])
                ->setNotifyLoanDisbursed($data['notifyLoanDisbursed'])
                ->setNotifyComment($data['notifyComment'])
                ->setNotifyLoanApplication($data['notifyLoanApplication'])
                ->setNotifyInviteAccepted($data['notifyInviteAccepted'])
                ->setNotifyLoanRepayment($data['notifyLoanRepayment'])
                ->save();
            return $lender->getPreferences();
    }

    public function getKarma(Lender $lender)
    {
        $totalImpact = $this->getMyImpact($lender);
        $totalComments = $this->getUserCommentCount($lender);
    }
    function getKarmaScore($userid){
        $total_impact = $this->getMyImpact($userid);
        $total_comments = $this->getUserCommentCount($userid);
        $karma = ($total_impact / 10) + $total_comments;
        return $karma;
    }

    public function getMyImpact(Lender $lender)
    {
        $invites = InviteQuery::create()
            ->filterByInvitee($lender)
            ->find();
        $invitesCount = $invites->count();
        $giftCards = GiftCardQuery::create()
            ->filterByLender($lender)
            ->filterByClaimed(true)
            ->where('GiftCard.recipient_id IS NOT IN ?' , $lender->getId())
            ->filterByStatus(1)
            ->find();
        $giftCardsCount = $giftCards->count();
        $inviteImpact = 0;
        $giftCardImpact = 0;
        $bidQuery = BidQuery::create();

        if ($invitesCount > 0) {
            foreach ($invites as $invite) {
                $totalBidAmount = $bidQuery
                    ->getTotalActiveBidAmount($invite->getInvitee());

                $totalOpenLoanBidAmount = $bidQuery
                    ->getTotalOpenLoanBidAmount($invite->getInvitee());

                $inviteImpact += $totalBidAmount + $totalOpenLoanBidAmount;
            }
        }

        if ($giftCardsCount > 0) {
            foreach ($giftCards as $giftCard) {
                $totalBidAmount = $bidQuery
                    ->getTotalActiveBidAmount($giftCard->getRecipient());

                $totalOpenLoanBidAmount = $bidQuery
                    ->getTotalOpenLoanBidAmount($giftCard->getRecipient());

                $giftCardImpact += $totalBidAmount + $totalOpenLoanBidAmount;
            }
        }
        //total amount this lender has lent for loans already funded
        $totalInvested = $bidQuery->getTotalActiveBidAmount($lender);
        $totalActiveLoanBidAmount = $bidQuery->getTotalOpenLoanBidAmount($lender); //total amount in not yet funded bids
        $totalImpact = $totalInvested + $totalActiveLoanBidAmount + $inviteImpact + $giftCardImpact;
        return $totalImpact;
    }

    public function getUserCommentCount(Lender $lender)
    {
            return BorrowerCommentQuery::create()
                ->filterByUser($lender->getUser())
                ->count();
    }
}
