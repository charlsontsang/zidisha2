<?php
namespace Zidisha\Borrower;

use Carbon\Carbon;
use DateTime;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Comment\BorrowerCommentQuery;
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
use Zidisha\Repayment\RepaymentService;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Upload\Upload;
use Zidisha\User\FacebookUser;
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
    private $repaymentService;

    public function __construct(FacebookService $facebookService, UserQuery $userQuery, BorrowerMailer $borrowerMailer,
        BorrowerSmsService $borrowerSmsService, LoanService $loanService, RepaymentService $repaymentService)
    {
        $this->facebookService = $facebookService;
        $this->userQuery = $userQuery;
        $this->borrowerMailer = $borrowerMailer;
        $this->borrowerSmsService = $borrowerSmsService;
        $this->loanService = $loanService;
        $this->repaymentService = $repaymentService;
    }

    public function joinBorrower($data)
    {
        $data += [
            'joinedAt' => new DateTime(),
        ];
        $borrower = new Borrower();

        PropelDB::transaction(function($con) use($data, $borrower) {
            $volunteerMentor = VolunteerMentorQuery::create()
                ->findOneByBorrowerId($data['volunteerMentorId']);
            $referrer = BorrowerQuery::create()
                ->findOneById($data['referrerId']);
            $facebookData = \Session::get('BorrowerJoin.facebookData');

            $user = new User();
            $user
                ->setJoinedAt($data['joinedAt'])
                ->setUsername($data['username'])
                ->setPassword($data['password'])
                ->setEmail($data['email'])
                ->setFacebookId($data['facebookId'])
                ->setRole('borrower');

            if ($facebookData) {
                $facebookUser = new FacebookUser();
                $facebookUser
                    ->setUser($user)
                    ->setEmail($facebookData['email'])
                    ->setAccountName($facebookData['name'])
                    ->setCity($facebookData['location'])
                    ->setBirthDate($facebookData['birthday'])
                    ->setFriendsCount($this->facebookService->getFriendCount())
                    ->setFirstPostDate($this->facebookService->getFirstPostDate());
                $facebookUser->save($con);
            }

            $borrower
                ->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setCountryId($data['countryId'])
                ->setVolunteerMentor($volunteerMentor)
                ->setReferrer($referrer)
                ->setUser($user);

            $profile = new Profile();
            $profile
                ->setAddress($data['address'])
                ->setAddressInstructions($data['addressInstructions'])
                ->setCity($data['city'])
                ->setNationalIdNumber($data['nationalIdNumber'])
                ->setPhoneNumber($data['phoneNumber'])
                ->setBusinessCategoryId($data['businessCategoryId'])
                ->setBusinessYears($data['businessYears'])
                ->setLoanUsage($data['loanUsage'])
                ->setBirthDate($data['birthDate'])
                ->setAlternatePhoneNumber($data['alternatePhoneNumber']);
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

            $borrower->save($con);

            $joinLog = new JoinLog();
            $joinLog
                ->setIpAddress($data['ipAddress'])
                ->setPreferredLoanAmount($data['preferredLoanAmount'])
                ->setPreferredInterestRate($data['preferredInterestRate'])
                ->setPreferredRepaymentAmount($data['preferredRepaymentAmount'])
                ->setBorrower($borrower);
            $joinLog->save($con);
        });

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

    public function editBorrower(Borrower $borrower, $data, $picture = null, $files = [])
    {
        $user = $borrower->getUser();
        $profile = $borrower->getProfile();
        
        $user->setEmail($data['email']);
        
        $profile
            ->setAboutMe($data['aboutMe'])
            ->setAboutBusiness($data['aboutBusiness']);

        if (!empty($data['password'])) {
            $borrower->getUser()->setPassword($data['password']);
        }

        if ($picture) {
            $upload = Upload::createFromFile($picture);
            $upload->setUser($user);

            $user->setProfilePicture($upload);
        }

        foreach ($files as $file) {
            $upload = Upload::createFromFile($file);
            $upload->setUser($user);
            
            $borrower->addUpload($upload);
        }

        $borrower->save();
    }

    public function deleteUpload(Borrower $borrower, Upload $upload)
    {
        $borrower->removeUpload($upload);
        $borrower->save();

        $upload->delete();
    }

    public function validateConnectingFacebookUser($facebookUser)
    {
        $checkUser = $this->userQuery
            ->filterByFacebookId($facebookUser['id'])
            ->_or()
            ->filterByEmail($facebookUser['email'])
            ->findOne();
        $facebookFriendsCount  = $this->facebookService->getFriendCount();
        $minimumFriendsRequired = \Setting::get('facebook.minimumFriends');

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

        if ($facebookFriendsCount < $minimumFriendsRequired) {
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

    public function verifyBorrower(Borrower $borrower, Datetime $verifiedAt = null)
    {
        $verifiedAt = $verifiedAt ?: new \DateTime();
        
        PropelDB::transaction(function() use($borrower, $verifiedAt) {
            $joinLog = $borrower->getJoinLog();
            $joinLog->setVerifiedAt($verifiedAt);
            $joinLog->save();

            $borrower->setVerified(true);
            $borrower->save();
            
            return $borrower;
        });
        
        return $borrower;
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
            ->findLastCompletedLoan($borrower);
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
                ->findLastLoan($invite->getInvitee());

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
                ->findLastLoan($invite->getInvitee());

            if (!empty($lastLoanOfInvitee)) {
                $repaymentRate = $this->loanService->getOnTimeRepaymentScore($invite->getInvitee());

                if ($repaymentRate >= $minRepaymentRate){
                    $count += 1;
                }
            }
        }
        return $count;
    }

    public function addVolunteerMentor(User $user)
    {
        $time=time();
        $user->setSubRole(User::SUB_ROLE_VOLUNTEER_MENTOR);
        $user->save();
        $borrower = $user->getBorrower();
        $volunteerMentor = VolunteerMentorQuery::create()
            ->findOneByBorrowerId($borrower->getId());
        if ($volunteerMentor) {
            $volunteerMentor->setActive(true);
            $volunteerMentor->save();
        } else {
            $newVM = new VolunteerMentor();
            $newVM
                ->setBorrowerVolunteer($borrower)
                ->setCountry($borrower->getCountry())
                ->setGrantDate($time);
            $newVM->save();
        }
    }

    public function removeVolunteerMentor(User $user)
    {
        $user->setSubRole(null);
        $user->save();
        $volunteerMentor = VolunteerMentorQuery::create()
            ->findOneByBorrowerId($user->getBorrower()->getId());
        if ($volunteerMentor) {
            $volunteerMentor->setActive(false);
            $volunteerMentor->save();
            return true;
        } else {
            return false;
        }
    }

    public function printLoanInArrears(Borrower $borrower)
    {
        $activeLoan =  $borrower->getActiveLoan();

        $isAnyLoanDefaulted = LoanQuery::create()
            ->filterByBorrowerId($borrower->getId())
            ->filterByDeletedByAdmin(false)
            ->filterByStatus(Loan::DEFAULTED)
            ->findOne();
        if ($isAnyLoanDefaulted) {
            return 'Loan in Arrears';
        }
        if ($activeLoan) {
            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($activeLoan);
            if ($repaymentSchedule->getOverDueInstallmentCount() > 1) {
                return 'Loan in Arrears';
            }
        }
        return 'Not in Arrears';
    }

    public function hasVMComment(Borrower $volunteerMentor, Borrower $borrower)
    {
        return BorrowerCommentQuery::create()
            ->filterByReceiverId($borrower->getId())
            ->filterByBorrowerId($volunteerMentor->getId())
            ->count();
    }
}
