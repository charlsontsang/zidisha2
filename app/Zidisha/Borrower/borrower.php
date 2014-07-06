<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Borrower as BaseBorrower;

class Borrower extends BaseBorrower
{

    const PAYMENT_COMPLETE = 1;
    const PAYMENT_INCOMPLETE = 2;
    const PAYMENT_PROCESSED = 3;
    const PAYMENT_FAILED = 4;
    const PAYMENT_DELETED = 5;

    public function getName(){
        return $this->getFirstName() . " " . $this->getLastName();
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
            'addressInstruction' => $profile->getAddressInstructions(),
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
}
