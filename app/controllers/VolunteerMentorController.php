<?php

class VolunteerMentorController extends BaseController
{

    public function getAssignedMembers()
    {

        return View::make('borrower.volunteer-mentor.assigned-members');
    }
}
