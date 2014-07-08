<?php

use Zidisha\Lender\Form\CreateGroupForm;
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

           $group =  $this->groupService->AddGroup($data);

            if (Input::hasFile('profile_picture_id')) {
                $image = Input::file('profile_picture_id');
                $this->groupService->uploadPicture($group, $image);
            }

            \Flash::success("Group created!");
            return Redirect::route('lender:groups:create');
        }
        return Redirect::route('lender:groups:create')->withForm($form);
    }
} 