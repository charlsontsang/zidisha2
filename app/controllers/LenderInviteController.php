<?php

use Zidisha\Lender\Form\Invite;
use Zidisha\Lender\InviteQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;

class LenderInviteController extends BaseController
{

    private $inviteForm;
    private $lenderService;
    private $inviteQuery;
    private $lenderQuery;

    public function __construct(
        Invite $inviteForm,
        LenderService $lenderService,
        InviteQuery $inviteQuery,
        LenderQuery $lenderQuery
    ) {

        $this->inviteForm = $inviteForm;
        $this->lenderService = $lenderService;
        $this->inviteQuery = $inviteQuery;
        $this->lenderQuery = $lenderQuery;
    }

    public function getInvite()
    {

        if ((Auth::check() && Auth::getUser()->getRole() == 'lender')) {
            $lender = Auth::user()->getLender();

            $invite_url = 'https://www.zidisha.org/' . 'i/' . $lender->getUser()->getUserName();

            $twitterParams = array(
                "url" => $invite_url . "?s=2",
                "text" => "Use this link to fund a Zidisha microloan project for free! @ZidishaInc",
            );
            $twitter_url = "http://twitter.com/share?" . http_build_query($twitterParams);

            $relative_invite_url = str_replace("https://www.", "", $invite_url);
            $facebook_url = "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode(
                    $relative_invite_url . "?s=3"
                );

            $invites = $this->inviteQuery->create()
                ->filterByLender($lender)
                ->find();

            $count_invites = count($invites);
            $count_joined_invites = 0;

            foreach ($invites as $invite) {
                if ($invite->getInviteeId() != null) {
                    $count_joined_invites += 1;
                }
            }

            return View::make(
                'lender.invite',
                ['form' => $this->inviteForm,]
                ,
                compact(
                    'invite_url',
                    'facebook_url',
                    'twitter_url',
                    'invites',
                    'count_invites',
                    'count_joined_invites'
                )
            );
        }
        return View::make('lender.invite_guest');

    }

    public function getHowItWorks()
    {
        return View::make('lender.how-it-works');
    }

    public function postInvite()
    {
        $form = $this->inviteForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $emails = explode(',', $data['emails']);
            $subject = $data['subject'];
            $lender = Auth::user()->getLender();
            $custom_message = $data['note'];
            foreach ($emails as $email) {
                $this->lenderService->lenderInviteViaEmail($lender, $email, $subject, $custom_message);
            }

            Flash::success('Invites Successfully sent!');
            return Redirect::route('lender:invite');
        }

        return Redirect::route('lender:invite')->withForm($form);
    }
}
