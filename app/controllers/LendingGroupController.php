<?php

use Zidisha\Comment\LendingGroupCommentService;
use Zidisha\Lender\Form\CreateGroupForm;
use Zidisha\Lender\Form\EditGroupForm;
use Zidisha\Lender\LendingGroupMemberQuery;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Lender\LendingGroupService;

class LendingGroupController extends BaseController
{
    private $createGroupForm;
    private $lendingGroupService;
    /**
     * @var LendingGroupCommentService
     */
    private $lenderGroupCommentService;

    public function __construct(CreateGroupForm $createGroupForm, LendingGroupService $lendingGroupService, LendingGroupCommentService $lenderGroupCommentService)
    {
        $this->createGroupForm = $createGroupForm;
        $this->lendingGroupService = $lendingGroupService;
        $this->lenderGroupCommentService = $lenderGroupCommentService;
    }

    public function getCreateGroup()
    {
        return View::make('lender.create-lending-group', ['form' => $this->createGroupForm,]);
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


        return View::make('lender.lending-groups', compact('paginator'));
    }

    public function getGroup($id)
    {
        $group = $receiver = LendingGroupQuery::create()
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

        $comments = $this->lenderGroupCommentService->getPaginatedComments($group, 1, 10);

        $commentType = 'lendingGroupComment';

        return View::make('lender.lending-group', compact('group', 'receiver', 'membersCount', 'members', 'leaderId', 'comments', 'commentType'));
    }

    public function joinGroup($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $lender = Auth::user()->getLender();

        $this->lendingGroupService->joinLendingGroup($group, $lender);

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
        $leaved = $this->lendingGroupService->leaveLendingGroup($group, $lender);
        if(!$leaved){
            \Flash::success("Leader can't leave the group!");
            return Redirect::route('lender:group', $group->getId());
        }

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

        if (!$group->isLeader($lender)){
            App::abort(404);
        }


        $editGroupForm = new EditGroupForm($group);
        return View::make('lender.edit-lending-group', ['form' => $editGroupForm,], compact('group'));
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

             $this->lendingGroupService->updateLendingGroup($group, $data, $image);

            \Flash::success("Group Edited!");
            return Redirect::route('lender:group', $id);
        }
        return Redirect::route('lender:groups:edit', $id)->withForm($form);
    }
} 