<?php

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Calculator\CreditLimitCalculator;
use Zidisha\Borrower\Form\InviteForm;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Credit\CreditSettingQuery;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\RepaymentService;

class BorrowerInviteController extends BaseBorrowerController
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
        $borrower = $this->getBorrower();

        $form = new InviteForm($borrower);
        
        $maxInviteesWithoutPayment = \Setting::get('invite.maxInviteesWithoutPayment');

        $invites = InviteQuery::create()
            ->filterByBorrower($borrower)
            ->find();

        $invitesCount = count($invites);
        $acceptedInvitesCount = 0;

        foreach ($invites as $invite) {
            if ($invite->getInviteeId()) {
                $acceptedInvitesCount += 1;
            }
        }

        $isEligible = $this->borrowerService->isEligibleToInvite($borrower);

        return View::make(
            'borrower.invite',
            ['form' => $form,]
            ,
            compact(
                'invites',
                'invitesCount',
                'acceptedInvitesCount',
                'isEligible',
                'maxInviteesWithoutPayment'
            )
        );
    }

    public function postInvite()
    {
        $borrower = $this->getBorrower();
        
        $isEligible = $this->borrowerService->isEligibleToInvite($borrower);
        
        if ($isEligible !== true) {
            \Flash::error('borrower.invite.flash.not-eligible');
            
            return Redirect::route('borrower:invite');
        }
        
        $form = new InviteForm($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $invite = $this->borrowerService->borrowerInviteViaEmail($borrower, $form->getData());

            Flash::success(\Lang::get('borrower.invite.flash.invite-success', ['email' => $invite->getEmail()]));
            return Redirect::route('borrower:invite');
        }

        return Redirect::route('borrower:invite')->withForm($form);
    }

    public function getInvites()
    {
        $borrower = $this->getBorrower();

        $minRepaymentRate = \Setting::get('invite.minRepaymentRate');
        $inviteesRepaymentRate = $this->borrowerService->getInviteeRepaymentRate($borrower);
        $successRate = number_format(($inviteesRepaymentRate)*100);

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($borrower->getCountry()->getCurrency());
        $calculator = new CreditLimitCalculator($borrower, $exchangeRate);
        $bonusEarned = $calculator->getInviteCredit();
        
        $currencyCode = $borrower->getCountry()->getCurrencyCode();
        $invites = InviteQuery::create()
            ->filterByBorrower($borrower)
            ->find();
        
        $loanService = $this->loanService;
        $repaymentService = $this->repaymentService;
        $borrowerInviteCredit = CreditSettingQuery::create()
            ->getBorrowerInviteCreditAmount($borrower->getCountry());

        return View::make(
            'borrower.invites',
            compact(
                'minRepaymentRate',
                'currencyCode',
                'successRate',
                'bonusEarned',
                'invites',
                'invites',
                'loanService',
                'repaymentService',
                'borrowerInviteCredit'
            )
        );
    }

    public function postDeleteInvite($id)
    {
        $borrower = $this->getBorrower();

        $invite = InviteQuery::create()
            ->filterByBorrower($borrower)
            ->filterById($id)
            ->findOne();
        
        if ($invite) {
            $invite->delete();
            \Flash::success(\Lang::get('borrower.invite.flash.invite-deleted', ['email' => $invite->getEmail()]));
        }

        return Redirect::route('borrower:invites');
    }
}
