<?php

use Zidisha\Admin\Setting;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Lender\Form\Invite;
use Zidisha\Lender\InviteQuery;
use Zidisha\Lender\InviteVisitQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;

class LenderInviteController extends BaseController
{

    private $inviteForm;
    private $lenderService;

    public function __construct(
        Invite $inviteForm,
        LenderService $lenderService
    ) {

        $this->inviteForm = $inviteForm;
        $this->lenderService = $lenderService;
    }

    public function getInvite()
    {
        if (!Auth::check() || Auth::getUser()->getRole() != 'lender') {
            return View::make('lender.invite-guest');
        }

        $lender = Auth::user()->getLender();

        $invite_url = route('lender:invitee', ['username' => $lender->getUser()->getUserName()]);

        $twitterParams = array(
            "url" => $invite_url . "?s=2",
            "text" => "Use this link to fund a Zidisha microloan project for free! @ZidishaInc",
        );
        $twitter_url = "http://twitter.com/share?" . http_build_query($twitterParams);

        $relative_invite_url = str_replace("https://www.", "", $invite_url);
        $facebook_url = "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode(
                $relative_invite_url . "?s=3"
            );

        $invites = InviteQuery::create()
            ->filterByLender($lender)
            ->find();

        $count_invites = count($invites);
        $count_joined_invites = 0;

        foreach ($invites as $invite) {
            if ($invite->getInviteeId()) {
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

            $emails = array_map('trim', explode(",", $data['emails']));
            $subject = $data['subject'];
            $lender = Auth::user()->getLender();
            $custom_message = $data['note'];

            $allInvites = InviteQuery::create()
                ->filterByLender($lender)
                ->select('email')
                ->find();

            $countInvites = 0;
            foreach ($emails as $email) {
                if (in_array($email, $allInvites->getData())) {
                    Flash::info(\Lang::get('comments.flash.already-invited', array('email' => $email)));
                } else {
                    $countInvites += 1;
                    $this->lenderService->lenderInviteViaEmail($lender, $email, $subject, $custom_message);
                }
            }

            Flash::success(\Lang::choice('comments.flash.invite-success', $countInvites, array('count' => $countInvites)));
            return Redirect::route('lender:invite');
        }

        return Redirect::route('lender:invite')->withForm($form);
    }

    public function getInvitee($username)
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
                ->filterByUsername($username)
            ->endUse()
            ->findOne();

        $lenderInviteVisit = InviteVisitQuery::create()
            ->findOneById(Session::get('lenderInviteVisitId'));

        if (!$lender) {
            return Redirect::route('/');
        }

        $ycAccountCredit = TransactionQuery::create()
            ->getCurrentBalance(Setting::get('site.YCAccountId'));

        if ($ycAccountCredit->getAmount() < 5000) {
            return View::make('lender.invite-inactive');
        }

        if (!Auth::check()) {
            $lenderInvite = $shareType = null;

            if (Request::query('h')) {
                $lenderInvite = InviteQuery::create()
                    ->filterByLender($lender)
                    ->findOneByHash(Request::query('h'));

                $shareType = $lenderInvite ? 1 : null;
            } else {
                $shareType = Request::query('s') ? : 0;
            }

            $isNewVisit = !$lenderInviteVisit || $lenderInviteVisit->getLenderId() != $lender->getId();

            if ($isNewVisit && $shareType !== null) {
                $lenderInviteVisit = $this->lenderService->addLenderInviteVisit(
                    $lender,
                    $shareType,
                    $lenderInvite
                );
                Session::put('lenderInviteVisitId', $lenderInviteVisit->getId());
                return Redirect::route('lender:invitee', ['username' => $lender->getUser()->getUsername()]);
            }
        }

        $carouselHeading = 'Send $25 to an entrepreneur for free';
        $buttonText = 'Sign up to redeem your credit';

        return View::make(
            'lender.invitee',
            compact('lender','carouselHeading','buttonText')
        );
    }
}
