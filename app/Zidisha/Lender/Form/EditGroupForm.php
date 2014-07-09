<?php

namespace Zidisha\Lender\Form;


use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LendingGroup;
use Zidisha\Lender\LendingGroupMemberQuery;
use Zidisha\User\UserQuery;

class EditGroupForm extends  AbstractForm
{
    private $group;

    public function __construct(LendingGroup $group) {

        $this->group = $group;
    }

    public function getRules($data)
    {
        return [
            'name'                  => 'required|unique:lending_groups,name,'. $this->group->getId() . ',id',
            'website'               => 'unique:lending_groups,website,'. $this->group->getId() . ',id',
            'about'                 => 'required',
            'groupProfilePictureId' => 'image|max:2048',
            'userId'              => 'required|in:' . implode(',', array_keys($this->getMembers())),
        ];
    }

    public function getDataFromRequest(Request $request) {
        $data = parent::getDataFromRequest($request);
        $data['groupProfilePictureId'] = $request->file('groupProfilePictureId');

        return $data;
    }
    public function getDefaultData()
    {
        return [
            'name'                  => $this->group->getName(),
            'website'               => $this->group->getWebsite(),
            'about'                 => $this->group->getAbout(),
        ];
    }

    public function getMembers()
    {
        $members = UserQuery::create()
            ->useLenderQuery()
                ->useLendingGroupMemberQuery()
                    ->filterByLendingGroup($this->group)
                    ->filterByLeaved(false)
                ->endUse()
            ->endUse()
            ->find()
            ->toKeyValue('id', 'username');

        return $members;
    }

}