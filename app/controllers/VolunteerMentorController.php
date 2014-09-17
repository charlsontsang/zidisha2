<?php

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\VolunteerMentorService;

class VolunteerMentorController extends BaseController
{

    private $volunteerMentorService;

    public function __construct(VolunteerMentorService $volunteerMentorService){

        $this->volunteerMentorService = $volunteerMentorService;
    }

    public function getAssignedMembers()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::User()->getBorrower();
        if (!$borrower) {
            App::abort(404);
        }

        $data['pendingMembers'] = $this->volunteerMentorService->getMentorPendingMembers($borrower);
        $data['assignedMembers'] = $this->volunteerMentorService->getMentorAssignedMembers($borrower);

        return View::make('borrower.volunteer-mentor.assigned-members', compact('data'));
    }
}
