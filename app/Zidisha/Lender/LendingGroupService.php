<?php

namespace Zidisha\Lender;


use Zidisha\Upload\Upload;
use Zidisha\User\UserQuery;

class LendingGroupService
{

    public function addGroup(Lender $creator, $data, $image)
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

        return $group;
    }

    public function wasMember(Lender $lender, LendingGroup $group)
    {

        $member = LendingGroupMemberQuery::create()
            ->filterByMember($lender)
            ->filterByLendingGroup($group)
            ->filterByLeaved(true)
            ->findOne();
        if($member){
            $member->setLeaved(false);
            $member->save();
            return true;
        }
        return false;
    }

    public function editGroup(LendingGroup $group, $data, $image)
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

}