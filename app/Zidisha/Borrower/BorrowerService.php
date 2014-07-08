<?php
namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\BorrowerQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Upload\Upload;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\PropelDB;

class BorrowerService
{
    private $facebookService;
    private $userQuery;
    private $borrowerMailer;
    private $borrowerSmsService;

    public function __construct(FacebookService $facebookService, UserQuery $userQuery, BorrowerMailer $borrowerMailer,
        BorrowerSmsService $borrowerSmsService )
    {
        $this->facebookService = $facebookService;
        $this->userQuery = $userQuery;
        $this->borrowerMailer = $borrowerMailer;
        $this->borrowerSmsService = $borrowerSmsService;
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
            $profile->setAddressInstructions($data['addressInstruction']);
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
            ->orderByAcceptedDate('desc')
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
}
