<?php
namespace Zidisha\Borrower;

use Faker\Provider\DateTime;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Borrower\Base\BorrowerQuery;
use Zidisha\Credit\CreditsEarnedQuery;
use Zidisha\Credit\CreditSetting;
use Zidisha\Credit\CreditSettingQuery;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentSchedule;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Upload\Upload;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\PropelDB;

class BorrowerService
{
    private $facebookService;
    private $userQuery;
    private $borrowerMailer;
    private $borrowerSmsService;
    private $loanService;

    public function __construct(FacebookService $facebookService, UserQuery $userQuery, BorrowerMailer $borrowerMailer,
        BorrowerSmsService $borrowerSmsService, LoanService $loanService)
    {
        $this->facebookService = $facebookService;
        $this->userQuery = $userQuery;
        $this->borrowerMailer = $borrowerMailer;
        $this->borrowerSmsService = $borrowerSmsService;
        $this->loanService = $loanService;
    }

    public function joinBorrower($data)
    {
        $volunteerMentor = VolunteerMentorQuery::create()
            ->findOneByBorrowerId($data['volunteerMentorId']);
        $referrer = BorrowerQuery::create()
            ->findOneById($data['referrerId']);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFacebookId($data['facebookId']);
        $user->setRole('borrower');

        $borrower = new Borrower();
        $borrower->setFirstName($data['firstName']);
        $borrower->setLastName($data['lastName']);
        $borrower->setCountryId($data['countryId']);
        $borrower->setVolunteerMentor($volunteerMentor);
        $borrower->setReferrer($referrer);
        $borrower->setUser($user);

        $profile = new Profile();
        $profile->setAddress($data['address']);
        $profile->setAddressInstructions($data['addressInstructions']);
        $profile->setCity($data['city']);
        $profile->setNationalIdNumber($data['nationalIdNumber']);
        $profile->setPhoneNumber($data['phoneNumber']);
        $profile->setAlternatePhoneNumber($data['alternatePhoneNumber']);
        $borrower->setProfile($profile);

        $communityLeader = new Contact();
        $communityLeader
            ->setType('communityLeader')
            ->setFirstName($data['communityLeader']['firstName'])
            ->setLastName($data['communityLeader']['lastName'])
            ->setPhoneNumber($data['communityLeader']['phoneNumber'])
            ->setDescription($data['communityLeader']['description']);
        $borrower->addContact($communityLeader);

        for ($i = 1; $i <= 3; $i++) {
            $familyMember = new Contact();
            $familyMember
                ->setType('familyMember')
                ->setFirstName($data['familyMember'][$i]['firstName'])
                ->setLastName($data['familyMember'][$i]['lastName'])
                ->setPhoneNumber($data['familyMember'][$i]['phoneNumber'])
                ->setDescription($data['familyMember'][$i]['description']);
            $borrower->addContact($familyMember);
        }

        for ($i = 1; $i <= 3; $i++) {
            $neighbor = new Contact();
            $neighbor
                ->setType('neighbor')
                ->setFirstName($data['neighbor'][$i]['firstName'])
                ->setLastName($data['neighbor'][$i]['lastName'])
                ->setPhoneNumber($data['neighbor'][$i]['phoneNumber'])
                ->setDescription($data['neighbor'][$i]['description']);
            $borrower->addContact($neighbor);
        }

        $borrower->save();

        $joinLog = new JoinLog();
        $joinLog
            ->setIpAddress($data['ipAddress'])
            ->setBorrower($borrower);
        $joinLog->save();

        $this->sendVerificationCode($borrower);

        $this->borrowerMailer->sendBorrowerJoinedConfirmationMail($borrower);
        
        if ($borrower->getVolunteerMentor()) {
            $this->borrowerMailer->sendBorrowerJoinedVolunteerMentorConfirmationMail($borrower);
        }
        
        foreach ($borrower->getContacts() as $contact) {
            $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
        }

        return $borrower;
    }

    public function updatePersonalInformation(Borrower $borrower, $data)
    {
        $updatedContacts = [];
        PropelDB::transaction(function($con) use($borrower, $data, $updatedContacts) {
            $profile = $borrower->getProfile();
            $profile->setAddress($data['address']);
            $profile->setAddressInstructions($data['addressInstructions']);
            $profile->setCity($data['city']);
            $profile->setNationalIdNumber($data['nationalIdNumber']);
            $profile->setPhoneNumber($data['phoneNumber']);
            $profile->setAlternatePhoneNumber($data['alternatePhoneNumber']);
            $profile->save($con);

            $communityLeader = $borrower->getCommunityLeader();

            if ($communityLeader->getPhoneNumber() != $data['communityLeader']['phoneNumber']) {
                $updatedContacts[] = $communityLeader;
            }

            $communityLeader
                ->setFirstName($data['communityLeader']['firstName'])
                ->setLastName($data['communityLeader']['lastName'])
                ->setPhoneNumber($data['communityLeader']['phoneNumber'])
                ->setDescription($data['communityLeader']['description']);
            $communityLeader->save($con);

            foreach ($borrower->getFamilyMembers() as $i => $familyMember) {
                if ($familyMember->getPhoneNumber() != $data['familyMember'][$i + 1]['phoneNumber']) {
                    $updatedContacts[] = $familyMember;
                }

                $familyMember
                    ->setFirstName($data['familyMember'][$i + 1]['firstName'])
                    ->setLastName($data['familyMember'][$i + 1]['lastName'])
                    ->setPhoneNumber($data['familyMember'][$i + 1]['phoneNumber'])
                    ->setDescription($data['familyMember'][$i + 1]['description']);
                $familyMember->save($con);
            }

            foreach ($borrower->getNeighbors() as $i => $neighbor) {

                if ($neighbor->getPhoneNumber() != $data['neighbor'][$i + 1]['phoneNumber']) {
                    $updatedContacts[] = $neighbor;
                }

                $neighbor
                    ->setFirstName($data['neighbor'][$i + 1]['firstName'])
                    ->setLastName($data['neighbor'][$i + 1]['lastName'])
                    ->setPhoneNumber($data['neighbor'][$i + 1]['phoneNumber'])
                    ->setDescription($data['neighbor'][$i + 1]['description']);
                $neighbor->save($con);
            }
        });

        foreach ($updatedContacts as $contact) {
            $this->borrowerSmsService->sendBorrowerJoinedContactConfirmationSms($contact);
        }
    }


    public function updateProfileInformation(Borrower $borrower, $data)
    {
        $borrower->setFirstName($data['firstName']);
        $borrower->setLastName($data['lastName']);
        $borrower->getUser()->setEmail($data['email']);
        $borrower->setCountryId($data['countryId']);

        if (!empty($data['password'])) {
            $borrower->getUser()->setPassword($data['password']);
        }

        $borrower->save();
    }

    public function editBorrower(Borrower $borrower, $data, $files = [])
    {
        $borrower->getUser()->setEmail($data['email']);
        $borrower->getProfile()->setAboutMe($data['aboutMe']);
        $borrower->getProfile()->setAboutBusiness($data['aboutBusiness']);

        if (!empty($data['password'])) {
            $borrower->getUser()->setPassword($data['password']);
        }

        if (\Input::hasFile('picture')) {
            $image = \Input::file('picture');

            $user = $borrower->getUser();

            if ($image) {
                $upload = Upload::createFromFile($image);
                $upload->setUser($user);

                $user->setProfilePicture($upload);
                //TODO: Test without user save
                $user->save();
            }
        }

        if ($files) {
            $user = $borrower->getUser();

            foreach ($files as $file) {
                $upload = Upload::createFromFile($file);
                $upload->setUser($user);
                $borrower->addUpload($upload);
            }
            $borrower->save();
        }

        $borrower->save();
    }

    public function deleteUpload(Borrower $borrower, Upload $upload)
    {
        $borrower->removeUpload($upload);
        $borrower->save();

        $upload->delete();
    }

    public function makeVolunteerMentor(Borrower $borrower)
    {
        $borrower->getUser()->setSubRole('volunteerMentorId');
        $borrower->save();
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
                $errors[] = \Lang::get('borrower-registration.account-already-linked');
            } else {
                $errors[] = \Lang::get('borrower-registration.email-address-already-linked');
            }
        }

        if (!$this->facebookService->isAccountOldEnough()) {
            $errors[] = \Lang::get('borrower-registration.account-not-old');
        }

        if (!$this->facebookService->hasEnoughFriends()) {
            $errors[] = \Lang::get('borrower-registration.does-not-have-enough-friends');
        }

        if (!$facebookUser['verified']) {
            $errors[] = \Lang::get('borrower-registration.facebook-email-not-verified');
        }

        return $errors;
    }

    protected function createVerificationToken()
    {
        return md5(uniqid(rand(), true));
    }

    public function sendVerificationCode(Borrower $borrower)
    {
        $hashCode = $this->createVerificationToken();

        $joinLog = $borrower->getJoinLog();
        $joinLog
            ->setVerificationCode($hashCode);
        $joinLog->save();

        $this->borrowerMailer->sendVerificationMail($borrower, $hashCode);
    }

    public function saveBorrowerGuest($formData, $sessionData)
    {
        $email = array_get($formData, 'email');

        $resumeCode = array_get($sessionData, 'resumeCode');
        if ($resumeCode) {
            $borrowerGuest = \Zidisha\Borrower\BorrowerGuestQuery::create()
                ->findOneByResumecode($resumeCode);
        } else {
            $resumeCode = md5(uniqid(rand(), true));

            $borrowerGuest = new BorrowerGuest();
        }

        $formData = serialize($formData);
        $sessionData = serialize($sessionData);

        $borrowerGuest
            ->setEmail($email)
            ->setResumecode($resumeCode)
            ->setSession($sessionData)
            ->setForm($formData);

        $borrowerGuest->save();

        $this->borrowerMailer->sendFormResumeLaterMail($email, $resumeCode);

        \Session::forget('BorrowerJoin');

        \Flash::info(\Lang::get('borrower.save-later.information-is-saved'));
        \Flash::info(
            \Lang::get(
                'borrower.save-later.application-resume-link' . ' ' . route('borrower:resumeApplication', $resumeCode)
            )
        );
        \Flash::info(\Lang::get('borrower.save-later.application-resume-code' . ' ' . $resumeCode));
        return \Redirect::action('BorrowerJoinController@getCountry');
    }

    public function addLoanFeedback($loanId, $data)
    {
        $loan = LoanQuery::create()
            ->findOneById($loanId);

        $feedbackMessage =  new FeedbackMessage();
        $feedbackMessage->setCc($data['cc'])
                ->setReplyTo($data['replyTo'])
                ->setSubject($data['subject'])
                ->setMessage($data['message'])
                ->setBorrowerEmail($data['borrowerEmail'])
                ->setSenderName($data['senderName'])
                ->setSentAt(new \DateTime())
                ->setLoan($loan)
                ->setType('loan')
                ->setLoanApplicant($loan->getBorrower());

        $feedbackMessage->save();

        $this->borrowerMailer->sendLoanFeedbackMail($feedbackMessage);

        return $feedbackMessage;
    }

    public function getFeedbackMessages(Loan $loan)
    {
        return FeedbackMessageQuery::create()
            ->filterByLoan($loan)
            ->orderByCreatedAt('desc')
            ->find();
    }


    public function getPreviousLoans(Borrower $borrower, Loan $loan)
    {
        $loans = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderByAcceptedAt('desc')
            ->find();

        $previousLoans = [];

        foreach($loans as $oneLoan){
            if($loan->getId() != $oneLoan->getId()){
                array_push($previousLoans, $oneLoan);
            }
        }

        return $previousLoans;
    }

    public function isFacebookRequired(Borrower $borrower)
    {
        $user = $borrower->getUser();
        $facebookId = $user->getFacebookId();

        $createdAt = $user->getCreatedAt();
        $requiredDate = \DateTime::createFromFormat('j-M-Y', '1-Jan-2014');

        $borrowerRequiresFacebook = $borrower->getCountry()->isFacebookRequired();

        return $borrowerRequiresFacebook && !$facebookId && ($createdAt > $requiredDate);
    }

    public function getFaqParameterArray()
    {
        $params = [];

        return $params;
    }
    
    public function borrowerInviteViaEmail(Borrower $borrower, $email, $subject, $message)
    {
        $borrowerInvite = new Invite();
        $borrowerInvite->setBorrower($borrower);
        $borrowerInvite->setEmail($email);
        $borrowerInvite->isInvited(true);
        $success = $borrowerInvite->save();

        if ($success) {
            $this->borrowerMailer->sendBorrowerInvite($borrower, $borrowerInvite, $subject, $message);
        }

        return $borrowerInvite;
    }

    public function isEligibleToInvite(Borrower $borrower)
    {
        //borrower must have repaid some amount to Zidisha
        $previousLoan = LoanQuery::create()
            ->getLastRepaidLoan($borrower);
        //in case where there is no previous loan, checks whether borrower has yet made any payments on current loan
        if(!empty($previousLoan)){
            $paid = $previousLoan;
        } else {
            $activeLoan = $borrower->getActiveLoan();
            $paid = InstallmentQuery::create()
                ->getPaidAmount($activeLoan);
        }
        //borrower must be currently active
        if (empty($paid) || $paid==0 || !$borrower->isActive()){
            $eligible = 0;
        } else {

            if (!$this->checkMaxInviteesWithoutPayment($borrower)) {
                return 0;
            }

            $repaymentRate = $this->loanService->getOnTimeRepaymentScore($borrower);
            $minRepaymentRate = Setting::get('invite.minRepaymentRate');

            if($repaymentRate<$minRepaymentRate){

                $eligible = 2;
            } else {
                $invitedMembers= $this->getInviteesWithLoans($borrower); //count only those invited members who have raised loans
                if (empty($invitedMembers)){
                    $eligible = 1;
                } elseif ($invitedMembers>=100){
                    $eligible = 0; //each person can recruit no more than 100 members with loans via invite function
                } else {
                    //if more than 10% of invited members do not meet repayment standard then this user is ineligible to invite more
                    $inviteesRepaymentRate = $this->getInviteeRepaymentRate($borrower);

                    if ($inviteesRepaymentRate < 0.9) {
                        $eligible = 3; //not eligible
                    }else{
                        $eligible = 1; //eligible
                    }
                }
            }
        }

        return $eligible;
    }

    public function checkMaxInviteesWithoutPayment(Borrower $borrower)
    {
        $maxInviteesWithoutPayment = Setting::get('invite.maxInviteesWithoutPayment');
        $q = 'SELECT COUNT(*) FROM borrower_invites i
              WHERE i.borrower_id = :borrowerId
                AND (i.invitee_id IS NULL
                     OR i.invitee_id = 0
                     OR NOT EXISTS (SELECT * FROM installment_payments r
                                    WHERE r.borrower_id = i.invitee_id))';

        $invitesCount = ( PropelDB::fetchNumber($q, [
                'borrowerId' => $borrower->getId(),
            ]));

        return $maxInviteesWithoutPayment > $invitesCount;
    }

    public function getInviteesWithLoans(Borrower $borrower)
    {
//set of all members invited by this member
        $invitees= InviteQuery::create()
            ->filterByBorrower($borrower)
            ->filterByInviteeId(null, Criteria::NOT_EQUAL)
            ->find();
        $totalLoans=0;

        foreach($invitees as $invite){
//checks repayment rate of invited members
            $lastLoanOfInvitee= LoanQuery::create()
                ->getLastLoan($invite->getInvitee());

            if(!empty($lastLoanOfInvitee)){
                $totalLoans += 1;
            }
        }
        return $totalLoans;
    }

    public function getInviteeRepaymentRate(Borrower $borrower)
    {

//counts all members invited by this user who meet admin on-time repayment rate standard
        $totalOnTimeInvitees=$this->countSuccessfulInvitees($borrower);
//counts total members invited by this user who have taken out loans
        $invitedMembers=$this->getInviteesWithLoans($borrower);
        if (empty($invitedMembers) || $invitedMembers==0){
            $successRate=1;
        } else {
//calculates percentage of members invited by this user who meet repayment rate standard
            $successRate=$totalOnTimeInvitees / $invitedMembers;
        }

        return $successRate;
    }

    function countSuccessfulInvitees(Borrower $borrower){
        global $session;

        //gets minimum on-time repayment rate needed to progress to larger loans as set by admin
        $minRepaymentRate = Setting::get('invite.minRepaymentRate');

        //set of all members invited by this member
        $invitees= InviteQuery::create()
            ->filterByBorrower($borrower)
            ->filterByInviteeId(null, Criteria::NOT_EQUAL)
            ->find();

        $count=0;
        foreach($invitees as $invite){
            //checks repayment rate of invited members
            $invite_lastloan= LoanQuery::create()
                ->getLastLoan($invite->getInvitee());

            if (!empty($lastLoanOfInvitee)) {
                $repaymentRate = $this->loanService->getOnTimeRepaymentScore($invite->getInvitee());

                if ($repaymentRate >= $minRepaymentRate){
                    $count += 1;
                }
            }
        }
        return $count;
    }

    public function getInviteCredit(Borrower $borrower)
    {
        global $session;
        $invitees= InviteQuery::create()
            ->filterByBorrower($borrower)
            ->filterByInviteeId(null, Criteria::NOT_EQUAL)
            ->find();
        $minRepaymentRate = Setting::get('invite.minRepaymentRate');

        $creditEarned = Money::create(0, $borrower->getCountry()->getCurrency());
        foreach($invitees as $invite){
            $lastLoanOfInvitee= LoanQuery::create()
                ->getLastLoan($invite->getInvitee());
            if(empty($lastLoanOfInvitee)){
                continue;
            }
            $repaymentRate = $this->loanService->getOnTimeRepaymentScore($invite->getInvitee());
            if($repaymentRate >= $minRepaymentRate)
            {
                $borrowerInviteCredit = CreditSettingQuery::create()
                    ->getBorrowerInviteCredit($borrower);
                $creditEarned = Money::create($borrowerInviteCredit->getLoanAmountLimit(), $borrower->getCountry()->getCurrency());
            }
        }
        return $creditEarned;
    }

    public function getVMCredit(Borrower $borrower)
    {
        $isVM = VolunteerMentorQuery::create()
            ->filterByBorrowerVolunteer($borrower)
            ->filterByStatus(VolunteerMentor::STATUS_APPROVED)
            ->findOne();
        $creditEarned = Money::create(0, $borrower->getCountry()->getCurrency());

        if ($isVM) {
            $mentees = BorrowerQuery::create()
                ->filterByVolunteerMentor($isVM)
                ->find();
            $borrowerInviteCredit = CreditSettingQuery::create()
                ->getBorrowerInviteCredit($borrower);
            $borrowerInviteCredit = Money::create($borrowerInviteCredit->getLoanAmountLimit(), $borrower->getCountry()->getCurrency());

            foreach ($mentees as $mentee) {
                //TODO
                //$brwrandLoandetail = $this->getBrwrAndLoanStatus($mentee['userid']);
//                if($brwrandLoandetail['loanActive'] == LOAN_ACTIVE && $brwrandLoandetail['overdue'] == 0){
                if($mentee->getActiveLoanId()){
                    $creditEarned = $creditEarned->add($borrowerInviteCredit);
                }
            }
        }
        return $creditEarned;
    }

    public function getCurrentCreditLimit(Borrower $borrower, Money $creditEarned, $addCreditEarned)
    {
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $isEverFunded = $this->isEverFundedLoan($borrower);
        $loanStatus = $borrower->getActivationStatus();
        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($borrower->getCountry()->getCurrency());
        $creditEarnedUSD = Converter::toUSD($creditEarned, $exchangeRate);
        if ($creditEarnedUSD->greaterThan(Money::create(1000, 'USD'))) {
            $creditEarnedUSD = Money::create(1000, 'USD');
        }
        $creditEarned = Converter::fromUSD($creditEarnedUSD, $borrower->getCountry()->getCurrency(), $exchangeRate);

        if($loanStatus == Loan::ACTIVE || $loanStatus == Loan::FUNDED || $loanStatus == Loan::OPEN){
//case where borrower has an active loan or fundraising application - we calculate credit limit based on current loan amount
            $loan = $borrower->getActiveLoan();
            $ontime = 1; //assume current loan will be repaid on time for purpose of displaying future credit limits

        }else{
//case where borrower has repaid one or more loans and has not yet posted an application for a new one - we calculate credit limit based on most recently repaid loan amount
            $loan= LoanQuery::create()
                ->getLastRepaidLoan($borrower);
            $ontime = $this->loanService->isRepaidOnTime($borrower, $loan);
        }
        $raisedUsdAmount = $loan->getRaisedUsdAmount();
        $raisedAmount = Converter::fromUSD($raisedUsdAmount, $loan->getCurrency(), $exchangeRate);
        $requestedAmount = $loan->getAmount();

        if ($isEverFunded == 0) {
//case where borrower has not yet received first loan disbursement - credit limit should equal admin 1st loan size plus invited borrower credit if applicable
            $isInvited = InviteQuery::create()
                ->filterByInvitee($borrower)
                ->findOne();
            if ($requestedAmount->greaterThan($firstLoanValue)) {
                if ($creditEarned->isPositive()) {
                    return $raisedAmount->add($creditEarned);
                }
                    return $raisedAmount;
            }
            $bonusCredit = 0 ;
            if (!empty($isInvited)) {
                $bonusCredit = Money::create(100, $borrower->getCountry()->getCurrency()); //adds bonus for new members who were invited by eligible existing members
            }
            $totalValue = Converter::fromUSD($firstLoanValue, $borrower->getCountry()->getCurrency(), $exchangeRate)->add($bonusCredit);
            return $totalValue;
        } elseif ($ontime != 1) {
            //case where last loan was repaid late - credit limit should equal last loan repaid on time or admin first loan setting, if no loan was ever repaid on time
            $previousAmount = $this->getPreviousLoanAmount($borrower, $loan, $exchangeRate);
            if (!empty($previousAmount) && $previousAmount->getAmount() > 10) {
                $currentLimit = $previousAmount;
            } else {
                $nativeFirstLoanValue = Converter::fromUSD($firstLoanValue, $borrower->getCountry()->getCurrency(), $exchangeRate);
                if (!$addCreditEarned) {
                    $currentLimit = $nativeFirstLoanValue;
                } else {
                    $currentLimit = $nativeFirstLoanValue->add($creditEarned);
                }
            }
            return $currentLimit;
        } else {
            //case where last loan was repaid on time, we next check whether monthly installment repayment rate meets threshold
            $repaymentRate = $this->loanService->getOnTimeRepaymentScore($borrower);
            $minRepaymentRate = Setting::get('invite.minRepaymentRate');

            if ( $repaymentRate < $minRepaymentRate) {
                //case where last loan repaid on time but monthly installment repayment rate is below admin threshold - loan size stays same
                if (!$addCreditEarned) {
                    $currentLimit = $raisedAmount;
                } else {
                    $currentLimit = $raisedAmount->add($creditEarned);
                }
            } else {
                //case where last loan repaid on time and overall repayment is above admin threshold - we next check whether the last loan was held long enough to qualify for credit limit increase, with the amount of time loans need to be held and size of increase both dependent on previous loan amount
                $disbursedDate = $loan->getDisbursedAt();
                $currentTime = new \DateTime();
                $disbursedAt = $disbursedDate ? $disbursedDate->getTimestamp() : null;
                $months = Utility::getIntervalInMonths($disbursedAt, $currentTime->getTimestamp());

                if ($raisedUsdAmount->lessThanOrEqual(Money::create(200, 'USD'))) {
                    $timeThreshold = Setting::get('loan.loanIncreaseThresholdLow');
                    $percentIncrease = Setting::get('loan.secondLoanPercentage');
                } elseif ($raisedUsdAmount->lessThanOrEqual(Money::create(1000, 'USD'))) {
                    $timeThreshold = Setting::get('loan.loanIncreaseThresholdMid');
                    $percentIncrease = Setting::get('loan.nextLoanPercentage');
                } elseif ($raisedUsdAmount->lessThanOrEqual(Money::create(3000, 'USD'))) {
                    $timeThreshold = Setting::get('loan.loanIncreaseThresholdHigh');
                    $percentIncrease = Setting::get('loan.nextLoanPercentage');
                } else {
                    $timeThreshold = Setting::get('loan.loanIncreaseThresholdTop');
                    $percentIncrease = Setting::get('loan.nextLoanPercentage');
                }

                if ($months < $timeThreshold) {
                    //if the loan has not been held long enough then borrower does not yet qualify for credit limit increase
                    if (!$addCreditEarned) {
                        $currentLimit = $raisedAmount;
                    } else {
                        $currentLimit = $raisedAmount->add($creditEarned);
                    }
                } else {
                    //case where last loan was repaid on time, overall repayment rate is above threshold and loan held for long enough to qualify for credit limit increase
                    $lastInstallmentAmount = InstallmentQuery::create()
                        ->getLastInstallmentAmount($borrower, $loan);
                    $lastInstallmentUsdAmount = Converter::toUSD(Money::create($lastInstallmentAmount, $loan->getCurrencyCode()), $exchangeRate)->getAmount();
                    if ($lastInstallmentAmount > ($raisedAmount->getAmount() * 0.1) && $lastInstallmentUsdAmount > 100) {
                        //case where more than 30% and $100 of last loan was paid in the last installment
                        if (!$addCreditEarned) {
                            $currentLimit = $raisedAmount;
                        } else {
                            $currentLimit = $raisedAmount->add($creditEarned);
                        }
                    } else {
                        if (!$addCreditEarned) {
                            $currentLimit = $raisedAmount->multiply($percentIncrease)->divide(100);
                            if ($loanStatus == Loan::OPEN) {
                                $currentLimit = $raisedAmount;
                            }
                        } else {
                            $currentLimit = $raisedAmount->multiply($percentIncrease)->divide(100)->add($creditEarned);
                        }
                    }
                }
            }
        }
        return $currentLimit;
    }

    public function isEverFundedLoan(Borrower $borrower)
    {
        $isFundedBefore = LoanQuery::create()
            ->getBeforeFundedLoan($borrower);

        if($isFundedBefore)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function getPreviousLoanAmount(Borrower $borrower, Loan $loan, ExchangeRate $exchangeRate)
    {
        $beforeFundedLoanCount = LoanQuery::create()
            ->getNumberOfBeforeFundedLoan($borrower);
        $firstLoanValue = Setting::get('loan.firstLoanValue');

        if(!$beforeFundedLoanCount)
        {
            /* it means it is first loan */
            $previousLoanAmount = $firstLoanValue;
        } else {
            /* it means it is next loan */
            // update by mohit on date 3-11-13 to fix get max loan amount credit from last loan  set expires=NULL
            if(!empty($loan)) {
                $amountNative = LoanQuery::create()
                    ->filterByBorrower($borrower)
                    ->filterByDeletedByAdmin(false)
                    ->filterByStatus(Loan::REPAID)
                    ->filterByExpiredAt(null)
                    ->select('AmountRaised')
                    ->withColumn('MAX(disbursed_amount)', 'AmountRaised')
                    ->filterById($loan->getId(), Criteria::NOT_EQUAL)
                    ->findOne();

                $amount = Converter::toUSD(Money::create($amountNative, $borrower->getCountry()->getCurrency(), $exchangeRate), $exchangeRate)->getAmount();

                if(!empty($amount) && $amount > 1){
                    $previousLoanAmount = $amount;
                }else{
                    if ($loan->getUsdAmount()->getAmount() > $firstLoanValue) {
                        $previousLoanAmount = $loan->getRaisedUsdAmount();
                    } else {
                        $previousLoanAmount = $firstLoanValue;
                    }
                }
            } else {
                $amountNative = LoanQuery::create()
                    ->filterByBorrower($borrower)
                    ->filterByDeletedByAdmin(false)
                    ->filterByStatus(Loan::REPAID)
                    ->filterByExpiredAt(null)
                    ->select('AmountRaised')
                    ->withColumn('MAX(disbursed_amount)', 'AmountRaised')
                    ->findOne();

                $previousLoanAmount = Converter::toUSD(Money::create($amountNative, $borrower->getCountry()->getCurrency(), $exchangeRate), $exchangeRate)->getAmount();
            }
        }
        $previousLoanAmount = Converter::fromUSD($previousLoanAmount, $loan->getCurrency(), $exchangeRate);
        return $previousLoanAmount;
    }
}
