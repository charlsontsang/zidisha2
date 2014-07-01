<?php
namespace Zidisha\Borrower;

use Illuminate\Support\Facades\Input;
use Zidisha\Upload\Upload;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\Facebook\FacebookService;

class BorrowerService
{
    /**
     * @var \Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;
    /**
     * @var \Zidisha\User\UserQuery
     */
    private $userQuery;

    public function __construct(FacebookService $facebookService, UserQuery $userQuery)
    {
        $this->facebookService = $facebookService;
        $this->userQuery = $userQuery;
    }

    public function joinBorrower($data)
    {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFacebookId($data['facebookId']);
        $user->setRole('borrower');

        $borrower = new Borrower();
        $borrower->setFirstName($data['firstName']);
        $borrower->setLastName($data['lastName']);
        $borrower->setCountryId($data['countryId']);
        $borrower->setUser($user);

        $profile = new Profile();
        $profile->setAddress($data['address']);
        $profile->setAddressInstruction($data['addressInstruction']);
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

        return $borrower;
    }

    public function editBorrower(Borrower $borrower, $data, $files = [])
    {
        $borrower->setFirstName($data['firstName']);
        $borrower->setLastName($data['lastName']);
        $borrower->getUser()->setEmail($data['email']);
        $borrower->getUser()->setUsername($data['username']);
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
        $borrower->getUser()->setSubRole('volunteerMentor');
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

        if(!$this->facebookService->isAccountOldEnough()){
            $errors[] = \Lang::get('borrower-registration.account-not-old');
        }

        if(!$this->facebookService->hasEnoughFriends()){
            $errors[] = \Lang::get('borrower-registration.does-not-have-enough-friends');
        }

        if(!$facebookUser['verified']){
            $errors[] = \Lang::get('borrower-registration.facebook-email-not-verified');
        }

        return $errors;
    }
}
