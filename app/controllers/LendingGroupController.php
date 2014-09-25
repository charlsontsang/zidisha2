<?php

use Zidisha\Balance\TransactionQuery;
use Zidisha\Comment\LendingGroupCommentService;
use Zidisha\Lender\Form\CreateGroupForm;
use Zidisha\Lender\Form\EditGroupForm;
use Zidisha\Lender\LenderService;
use Zidisha\Lender\LendingGroupMemberQuery;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Lender\LendingGroupService;

class LendingGroupController extends BaseController
{
    private $createGroupForm;
    private $lendingGroupService;
    private $lenderGroupCommentService;
    private $lenderService;

    public function __construct(CreateGroupForm $createGroupForm, LendingGroupService $lendingGroupService, LendingGroupCommentService $lenderGroupCommentService, LenderService $lenderService)
    {
        $this->createGroupForm = $createGroupForm;
        $this->lendingGroupService = $lendingGroupService;
        $this->lenderGroupCommentService = $lenderGroupCommentService;
        $this->lenderService = $lenderService;
    }

    public function getCreateGroup()
    {
        return View::make('lender.groups.create', ['form' => $this->createGroupForm,]);
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

            return Redirect::route('lender:group:create:success', $group->getId());
        }
        return Redirect::route('lender:groups:create')->withForm($form);
    }

    public function getCreateSuccess($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);
        if (!$group) {
            App::abort(404);
        }

        $groupUrl = route('lender:group', $id);
        $groupName = $group->getName();

        $twitterParams = array(
            "url" => $groupUrl,
            "text" => "Just started a $groupName lending group @ZidishaInc",
        );
        $twitterUrl = "http://twitter.com/share?" . http_build_query($twitterParams);

        $relativeInviteUrl = str_replace("https://www.", "", $groupUrl);
        $relativeInviteUrl = str_replace("http://www.", "", $relativeInviteUrl);
        $facebookUrl = "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode($relativeInviteUrl);
        $mailUrl = "mailto:?body=%0D%0A%0D%0A%0D%0A".$groupUrl;

        $successMessage = "You just created the " . $groupName . " lending group!";

        return View::make('lender.groups.success', compact('group', 'twitterUrl', 'facebookUrl', 'mailUrl', 'successMessage'));
    }

    public function getJoinSuccess($id)
    {
        $group = LendingGroupQuery::create()
            ->findOneById($id);
        if (!$group) {
            App::abort(404);
        }

        $groupUrl = route('lender:group', $id);
        $groupName = $group->getName();

        $twitterParams = array(
            "url" => $groupUrl,
            "text" => "Just joined $groupName lending group via @ZidishaInc",
        );
        $twitterUrl = "http://twitter.com/share?" . http_build_query($twitterParams);

        $relativeInviteUrl = str_replace("https://www.", "", $groupUrl);
        $relativeInviteUrl = str_replace("http://www.", "", $relativeInviteUrl);
        $facebookUrl = "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode($relativeInviteUrl);
        $mailUrl = "mailto:?body=%0D%0A%0D%0A%0D%0A".$groupUrl;

        $successMessage = "You just joined the " . $groupName . " lending group!";

        return View::make('lender.groups.success', compact('group', 'twitterUrl', 'facebookUrl', 'mailUrl', 'successMessage'));
    }

    public function getGroups()
    {
        $page = Request::query('page') ? : 1;

        $paginator = LendingGroupQuery::create()
            ->orderByCreatedAt()
            ->paginate($page, 10);

        foreach ($paginator as $group) {
            $groupsImpacts[$group->getId()] = $this->lendingGroupService->getGroupImpacts($group->getId());
        }


        return View::make('lender.groups.index', compact('paginator', 'groupsImpacts'));
    }

    public function getGroup($id)
    {
        $group = $receiver = LendingGroupQuery::create()
            ->findOneById($id);

        if (!$group) {
            App::abort(404);
        }

        $page = 1;
        if (Input::has('page')) {
            $page = Input::get('page');
        }

        $canPostComment  = false;
        $canReplyComment = false;

        if(Auth::check()) {
            $user = Auth::user();
            $canPostComment  = $user->isLender();
            $canReplyComment = $user->isLender();
        }

        $members = LendingGroupMemberQuery::create()
            ->filterByLendingGroup($group)
            ->filterByLeaved(false)
            ->find();
        $membersCount = count($members);
        $leaderId = $group->getLeader()->getId();

        $comments = $this->lenderGroupCommentService->getPaginatedComments($group, $page, 10);

        $commentType = 'lendingGroupComment';

        $groupImpacts = $this->lendingGroupService->getGroupImpacts($id);

        return View::make('lender.groups.profile', compact('group', 'receiver', 'membersCount', 'members', 'leaderId', 'comments', 'commentType', 'canPostComment', 'canReplyComment', 'groupImpacts'));
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

        return Redirect::route('lender:group:join:success', $group->getId());
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
            \Flash::success("Please use the \"Edit Group\" page to transfer leadership to another member before leaving. Thanks!");
            return Redirect::route('lender:group', $group->getId());
        }

        \Flash::success("You have left this group.");
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
        return View::make('lender.groups.edit', ['form' => $editGroupForm,], compact('group'));
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

            \Flash::success("Group successfully updated.");
            return Redirect::route('lender:group', $id);
        }
        return Redirect::route('lender:groups:edit', $id)->withForm($form);
    }
} 