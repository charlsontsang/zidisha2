<?php

use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\InviteForm;
use Zidisha\Borrower\InviteQuery;

class BorrowerInviteController extends BaseController
{
    private $borrowerService;

    public function __construct(BorrowerService $borrowerService) {

        $this->borrowerService = $borrowerService;
    }

    public function getInvite()
    {
        if (!Auth::check() || Auth::getUser()->getRole() != 'borrower') {
            return View::make('lender.invite-guest');
        }

        $borrower = Auth::user()->getBorrower();
        $form = new InviteForm($borrower);
        $maxInviteesWithoutPayment = \Setting::get('invite.maxInviteesWithoutPayment');

        $invites = InviteQuery::create()
            ->filterByBorrower($borrower)
            ->find();

        $count_invites = count($invites);
        $count_joined_invites = 0;

        foreach ($invites as $invite) {
            if ($invite->getInviteeId()) {
                $count_joined_invites += 1;
            }
        }

        $isEligible = $this->borrowerService->isEligibleToInvite($borrower);

        return View::make(
            'borrower.invite',
            ['form' => $form,]
            ,
            compact(
                'invites',
                'count_invites',
                'count_joined_invites',
                'isEligible',
                'maxInviteesWithoutPayment'
            )
        );
    }

    public function postInvite()
    {
        $borrower = Auth::user()->getBorrower();
        $form = new InviteForm($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $email = $data['email'];
            $borrowerName = $data['borrowerName'];
            $borrowerEmail = $data['borrowerEmail'];
            $subject = $data['subject'];
            $message = $data['note'];

                    $this->borrowerService->borrowerInviteViaEmail($borrower, $email, $subject, $message);

            Flash::success(\Lang::choice('comments.flash.invite-success', 1, array('count' => 1)));
            return Redirect::route('borrower:invite');
        }

        return Redirect::route('borrower:invite')->withForm($form);
    }
} 