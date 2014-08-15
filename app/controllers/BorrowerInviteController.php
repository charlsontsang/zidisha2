<?php

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerGuestQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\InviteForm;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Credit\CreditSettingQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\RepaymentService;

class BorrowerInviteController extends BaseController
{
    private $borrowerService;
    private $loanService;
    private $repaymentService;

    public function __construct(BorrowerService $borrowerService, LoanService $loanService, RepaymentService $repaymentService) {

        $this->borrowerService = $borrowerService;
        $this->loanService = $loanService;
        $this->repaymentService = $repaymentService;
    }

    public function getInvite()
    {
        if (!Auth::check() || Auth::getUser()->getRole() != 'borrower') {
            return View::make('lender.invite-guest');
        }

        $borrower = Auth::user()->getBorrower();

        if (!$borrower) {
            App::abort(404, 'Bad Request');
        }
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

        if (!$borrower) {
            App::abort(404, 'Bad Request');
        }
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

    public function getInvites($id = null)
    {
        /** @var $borrower Borrower */
        $borrower = Auth::user()->getBorrower();
        if (!$borrower) {
            App::abort(404, 'Bad Request');
        }
        if ($id) {
            $invite = InviteQuery::create()
                ->filterByBorrower($borrower)
                ->filterById($id)
                ->findOne();
            if ($invite) {
                $invite->delete();
            }
        }
        $page = Request::query('page') ? : 1;

        $minRepaymentRate = \Setting::get('invite.minRepaymentRate');
        $currencyCode = $borrower->getCountry()->getCurrencyCode();
        $inviteesRepaymentRate = $this->borrowerService->getInviteeRepaymentRate($borrower);
        $successRate = number_format(($inviteesRepaymentRate)*100);
        $creditEarned = $this->borrowerService->getInviteCredit($borrower);
        $bonusEarned = $currencyCode . " " . $creditEarned ;
        $paginator = InviteQuery::create()
            ->filterByBorrower($borrower)
            ->paginate($page, 10);
        $loanService = $this->loanService;
        $repaymentService = $this->repaymentService;
        $borrowerInviteCredit = CreditSettingQuery::create()
            ->getBorrowerInviteCredit($borrower);

        return View::make(
            'borrower.invites',
            compact(
                'minRepaymentRate',
                'currencyCode',
                'successRate',
                'bonusEarned',
                'invites',
                'paginator',
                'loanService',
                'repaymentService',
                'borrowerInviteCredit'
            )
        );
    }
}
