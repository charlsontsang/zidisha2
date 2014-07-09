<?php

use Zidisha\Lender\Form\CreateGroupForm;
use Zidisha\Lender\Form\EditGroupForm;
use Zidisha\Lender\LendingGroupMemberQuery;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Lender\LendingGroupService;

class LendingGroupController extends BaseController
{
    private $createGroupForm;
    private $lendingGroupService;

    public function __construct(CreateGroupForm $createGroupForm, LendingGroupService $lendingGroupService)
    {
        $this->createGroupForm = $createGroupForm;
        $this->lendingGroupService = $lendingGroupService;
    }

    public function getCreateGroup()
    {
        return View::make('lender.create-group', ['form' => $this->createGroupForm,]);
    }

    public function postCreateGroup()
    {
        $form = $this->createGroupForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $creator = \Auth::user()->getLender();
            $image = null;

            if (Input::hasFile('groupProfilePictureId')) {
                $image = Input::file('groupProfilePictureId');

            }

            $group =  $this->lendingGroupService->addLendingGroup($creator, $data, $image);

            \Flash::success("Group created!");
            return Redirect::route('lender:group', $group->getId());
        }
        return Redirect::route('lender:groups:create')->withForm($form);
    }

    public function getGroups()
    {
        $page = Request::query('page') ? : 1;

        $paginator = LendingGroupQuery::create()
            ->orderByCreatedAt()
            ->paginate($page, 10);


        return View::make('lender.groups', compact('paginator'));
    }

    public function getGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $members = LendingGroupMemberQuery::create()
            ->filterByLendingGroup($group)
            ->filterByLeaved(false)
            ->find();
        $membersCount = count($members);
        $leaderId = $group->getLeader()->getId();

        return View::make('lender.group', compact('group', 'membersCount', 'members', 'leaderId'));
    }

    public function joinGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $lender = Auth::user()->getLender();

        $this->lendingGroupService->joinLendingGroup($lender, $group);

        \Flash::success("Successfully Joined!");
        return Redirect::route('lender:group', $group->getId());
    }

    public function leaveGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $lender = Auth::user()->getLender();
        $this->lendingGroupService->leaveLendingGroup($group, $lender);

        $member = LendingGroupMemberQuery::create()
            ->filterByLendingGroup($group)
            ->filterByMember($lender)
            ->findone();

        $member->setLeaved(true);
        $member->save();

        \Flash::success("Successfully Leaved!");
        return Redirect::route('lender:group', $group->getId());
    }

    public function getEditGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }
        $lender = Auth::user()->getLender();

        if ($lender != $group->getLeader()){
            App::abort(404);
        }


        $editGroupForm = new EditGroupForm($group);
        return View::make('lender.edit-group', ['form' => $editGroupForm,], compact('group'));
    }

    public function postEditGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }
        $lender = Auth::user()->getLender();

        if ($lender != $group->getLeader()){
            App::abort(404);
        }

        $form = new EditGroupForm($group);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $image = null;

            if (Input::hasFile('groupProfilePictureId')) {
                $image = Input::file('groupProfilePictureId');
            }

             $this->lendingGroupService->editLendingGroup($group, $data, $image);

            \Flash::success("Group Edited!");
            return Redirect::route('lender:group', $id);
        }
        return Redirect::route('lender:groups:edit', $id)->withForm($form);
    }
} 