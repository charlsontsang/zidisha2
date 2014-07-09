<?php

use Zidisha\Lender\Form\CreateGroupForm;
use Zidisha\Lender\GroupMemberQuery;
use Zidisha\Lender\GroupQuery;
use Zidisha\Lender\GroupService;

class GroupController extends BaseController
{
    private $createGroupForm;
    private $groupService;

    public function __construct(CreateGroupForm $createGroupForm, GroupService $groupService)
    {
        $this->createGroupForm = $createGroupForm;
        $this->groupService = $groupService;
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

            $group =  $this->groupService->addGroup($creator, $data, $image);

            \Flash::success("Group created!");
            return Redirect::route('lender:group', $group->getId());
        }
        return Redirect::route('lender:groups:create')->withForm($form);
    }

    public function getGroups()
    {
        $page = Request::query('page') ? : 1;

        $paginator = GroupQuery::create()
            ->orderByCreatedAt()
            ->paginate($page, 10);


        return View::make('lender.groups', compact('paginator'));
    }

    public function getGroup($id)
    {
        $group = GroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $members = GroupMemberQuery::create()
            ->filterByGroup($group)
            ->filterByLeaved(false)
            ->find();
        $membersCount = count($members);


        return View::make('lender.group', compact('group', 'membersCount', 'members'));
    }

    public function joinGroup($id)
    {
        $group = GroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $lender = Auth::user()->getLender();

        if($this->groupService->wasMember($lender, $group)){

        }else{
            $member = new \Zidisha\Lender\GroupMember();
            $member->setMember($lender)
                ->setGroup($group);
            $member->save();
        }

        \Flash::success("Successfully Joined!");
        return Redirect::route('lender:group', $group->getId());
    }

    public function leaveGroup($id)
    {
        $group = GroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $lender = Auth::user()->getLender();

        $member = GroupMemberQuery::create()
            ->filterByGroup($group)
            ->filterByMember($lender)
            ->findone();

        $member->setLeaved(true);
        $member->save();

        \Flash::success("Successfully Leaved!");
        return Redirect::route('lender:group', $group->getId());
    }
} 