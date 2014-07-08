<?php

namespace Zidisha\Lender;


use Zidisha\Upload\Upload;

class GroupService
{

    public function AddGroup($data)
    {

        $creator = \Auth::user()->getLender();

        $group = new Group();
        $group->setName($data['name'])
            ->setAbout($data['about'])
            ->setWebsite($data['website'] ? $data['website'] : null )
            ->setCreator($creator);

        $group->save();

        return $group;
    }

    public function uploadPicture(Group $group, $image)
    {
        $user = $group->getCreator()->getUser();
        if ($image) {

            $upload = Upload::createFromFile($image);
            $upload->setUser($user);
            $group->setGroupProfilePicture($upload);
            $group->save();
        }
    }
}