<?php

namespace Zidisha\Lender;


use Zidisha\Upload\Upload;
use Zidisha\User\UserQuery;

class LendingGroupService
{

    public function addLendingGroup(Lender $creator, $data, $image)
    {

        $group = new LendingGroup();
        $group->setName($data['name'])
            ->setAbout($data['about'])
            ->setWebsite($data['website'] ? $data['website'] : null )
            ->setCreator($creator)
            ->setLeader($creator);

        if ($image) {
            $user = $group->getCreator()->getUser();
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);
            $group->setGroupProfilePicture($upload);
        }

        $group->save();
        $this->joinLendingGroup($group, $creator);

        return $group;
    }

    public function editLendingGroup(LendingGroup $group, $data, $image)
    {
        $leader = UserQuery::create()
            ->findOneById($data['userId']);

        $group->setName($data['name'])
            ->setAbout($data['about'])
            ->setWebsite($data['website'] ? $data['website'] : null)
            ->setLeader($leader->getLender());

        if ($image) {
            $user = $group->getCreator()->getUser();
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);
            $group->setGroupProfilePicture($upload);
        }
        $group->save();
    }

    public function joinLendingGroup(LendingGroup $group, Lender $lender)
    {
        $member = LendingGroupMemberQuery::create()
            ->filterByMember($lender)
            ->filterByLendingGroup($group)
            ->findOne();
        
        if($member){
            if($member->getLeaved() == false){
                return;
            }
            $member->setLeaved(false);
            $member->save();
        }else{
            $member = new LendingGroupMember();
            $member->setMember($lender)
                ->setLendingGroup($group);
            $member->save();
        }
    }

    public function leaveLendingGroup(LendingGroup $group, Lender $lender)
    {
        $member = LendingGroupMemberQuery::create()
            ->filterByLendingGroup($group)
            ->filterByMember($lender)
            ->findone();

        if($member){
            if($group->getLeader()->getId() == $member->getMemberId()){
                return false;
            }
            $member->setLeaved(true);
            $member->save();
            return true;
        }
    }

}