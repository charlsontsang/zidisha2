<?php

namespace Zidisha\Lender;


use Zidisha\Upload\Upload;

class GroupService
{

    public function addGroup(Lender $creator, $data, $image)
    {

        $group = new Group();
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

    public function wasMember(Lender $lender, Group $group)
    {

        $member = GroupMemberQuery::create()
            ->filterByMember($lender)
            ->filterByGroup($group)
            ->filterByLeaved(true)
            ->findOne();
        if($member){
            $member->setLeaved(false);
            $member->save();
            return true;
        }
        return false;
    }
}