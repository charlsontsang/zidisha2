<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Borrower as BaseBorrower;
use Zidisha\Comment\CommentReceiverInterface;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Loan\Loan;

class Borrower extends BaseBorrower implements CommentReceiverInterface
{

    // TODO move to Payment?
    const PAYMENT_COMPLETE = 1;
    const PAYMENT_INCOMPLETE = 2;
    const PAYMENT_PROCESSED = 3;
    const PAYMENT_FAILED = 4;
    const PAYMENT_DELETED = 5;

    const ACTIVATION_PENDING    = 'pending';
    const ACTIVATION_INCOMPLETE = 'incomplete';
    const ACTIVATION_REVIEWED   = 'reviewed';
    const ACTIVATION_APPROVED   = 'approved';
    const ACTIVATION_DECLINED   = 'declined';

    public function getName(){
        return ucwords(strtolower($this->getFirstName() . " " . $this->getLastName()));
    }
    
    public function getCommunityLeader()
    {
        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() == 'communityLeader') {
                return $contact;
            }
        }
        
        return null;
    }

    public function getFamilyMembers()
    {
        $familyMembers = [];

        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() == 'familyMember') {
                $familyMembers[] = $contact;
            }
        }

        return $familyMembers;
    }

    public function getNeighbors(){
        $neighbors = [];

        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() == 'neighbor') {
                $neighbors[] = $contact;
            }
        }

        return $neighbors;
    }

    public function getPersonalInformation()
    {
        $profile = $this->getProfile();

        $data = [
            'address' => $profile->getAddress(),
            'addressInstructions' => $profile->getAddressInstructions(),
            'city' => $profile->getCity(),
            'nationalIdNumber' => $profile->getNationalIdNumber(),
            'phoneNumber' => $profile->getPhoneNumber(),
            'alternatePhoneNumber' => $profile->getAlternatePhoneNumber(),
        ];


        $communityLeader = $this->getCommunityLeader();
        $data['communityLeader_firstName'] = $communityLeader->getFirstName();
        $data['communityLeader_lastName'] = $communityLeader->getLastName();
        $data['communityLeader_phoneNumber'] = $communityLeader->getPhoneNumber();
        $data['communityLeader_description'] = $communityLeader->getDescription();

        $familyMembers = $this->getFamilyMembers();

        foreach ($familyMembers as $i => $contact) {
            $data["familyMember_".($i+1)."_firstName"] = $contact->getFirstName();
            $data["familyMember_".($i+1)."_lastName"] = $contact->getLastName();
            $data["familyMember_".($i+1)."_phoneNumber"] = $contact->getPhoneNumber();
            $data["familyMember_".($i+1)."_description"] = $contact->getPhoneNumber();
        }

        $neighbors = $this->getNeighbors();

        foreach ($neighbors as $i => $contact) {
            $data["neighbor_".($i+1)."_firstName"] = $contact->getFirstName();
            $data["neighbor_".($i+1)."_lastName"] = $contact->getLastName();
            $data["neighbor_".($i+1)."_phoneNumber"] = $contact->getPhoneNumber();
            $data["neighbor_".($i+1)."_description"] = $contact->getPhoneNumber();
        }

        return $data;
    }

    public function isActivationPending()
    {
        return $this->getActivationStatus() == static::ACTIVATION_PENDING;
    }

    public function isActivationIncomplete()
    {
        return $this->getActivationStatus() == static::ACTIVATION_INCOMPLETE;
    }

    public function isActivationReviewed()
    {
        return $this->getActivationStatus() == static::ACTIVATION_REVIEWED;
    }
    
    public function isActivationDeclined()
    {
        return $this->getActivationStatus() == static::ACTIVATION_DECLINED;
    }
    
    public function isActivationApproved()
    {
        return $this->getActivationStatus() == static::ACTIVATION_APPROVED;
    }

    public function isVerified()
    {
        return $this->getVerified();
    }

    public function getCommentReceiverId()
    {
        return $this->getId();
    }

    public function hasActiveLoan()
    {
        return (boolean) $this->getActiveLoanId();
    }

    public function isNewLoanAllowed()
    {
        return in_array(
            $this->getLoanStatus(),
            [Loan::OPEN, Loan::CANCELED, Loan::REPAID, Loan::EXPIRED, Loan::NO_LOAN]
        );
    }

    public function getContactsList()
    {
        $contacts = '';
        $communityLeader = $this->getCommunityLeader();
        if ($communityLeader) {
            $contacts .= sprintf("\n%s %s of %s, %s",$communityLeader->getFirstName(), $communityLeader->getLastName(), $communityLeader->getDescription(), $communityLeader->getPhoneNumber());
        }

        $familyMembers = $this->getFamilyMembers();
        /** @var Contact $familyMember */
        foreach ($familyMembers as $familyMember) {
            $contacts .= sprintf("\n%s %s of %s, %s",$familyMember->getFirstName(), $familyMember->getLastName(), $familyMember->getDescription(), $familyMember->getPhoneNumber());
        }

        $neighbors = $this->getNeighbors();
        /** @var Contact $neighbor */
        foreach ($neighbors as $neighbor) {
            $contacts .= sprintf("\n%s %s of %s, %s",$neighbor->getFirstName(), $neighbor->getLastName(), $neighbor->getDescription(), $neighbor->getPhoneNumber());
        }

        if ($this->getReferrerId()) {
            $referrer = $this->getReferrer();
            $contacts .= "\n".$referrer->getName()." ".$referrer->getProfile()->getPhoneNumber();
        }

        if ($this->getVolunteerMentorId()) {
            $volunteerMentor = $this->getVolunteerMentor()->getBorrowerVolunteer();
            $contacts .= "\n".$volunteerMentor->getName()." ".$volunteerMentor->getProfile()->getPhoneNumber();
        }

        $invitee = InviteQuery::create()
            ->getInvitee($this->getId());
        if ($invitee) {
            $contacts.="\n".$invitee->getBorrower()->getName()." ".$invitee->getBorrower()->getProfile()->getPhoneNumber();
        }

        return $contacts;
        //TODO
//        if(!empty($bdetail['fb_data'])){
//            $fb_data= unserialize(base64_decode($bdetail['fb_data']));
//            if(isset($fb_data['user_friends']['data'])){
//                $friends= count($fb_data['user_friends']['data']);
//            }else{
//                $friends=count($fb_data['user_friends']);
//            }
//            $params.="\n".$friends." friends linked to ".$fb_data['user_profile']['name']." Facebook profile";
//        }
    }

}
