<?php

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\VolunteerMentorService;
use Zidisha\Repayment\RepaymentService;

class VolunteerMentorController extends BaseController
{

    private $volunteerMentorService;
    private $borrowerService;
    private $repaymentService;

    public function __construct(VolunteerMentorService $volunteerMentorService, BorrowerService $borrowerService,
                    RepaymentService $repaymentService){

        $this->volunteerMentorService = $volunteerMentorService;
        $this->borrowerService = $borrowerService;
        $this->repaymentService = $repaymentService;
    }

    public function getAssignedMembers($vmId = null)
    {
        if ($vmId) {
            $borrower = \Zidisha\Borrower\BorrowerQuery::create()
                ->findOneById($vmId);
        } else {
            /** @var Borrower $borrower */
            $borrower = \Auth::User()->getBorrower();
        }
        if (!$borrower) {
            App::abort(404);
        }

        $data['pendingMembers'] = $this->volunteerMentorService->getMentorPendingMembers($borrower);
        $data['assignedMembers'] = $this->volunteerMentorService->getMentorAssignedMembers($borrower);
        $data['borrowerService'] = $this->borrowerService;
        $data['repaymentService'] = $this->repaymentService;

        return View::make('borrower.volunteer-mentor.assigned-members', compact('data', 'borrower'));
    }
}
