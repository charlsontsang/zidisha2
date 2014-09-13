<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Balance\Transaction;
use Zidisha\Lender\InviteQuery;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;


/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'scheduled_jobs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class InviteeOwnFunds extends ScheduledJob
{
    const COUNT = 2;
    /**
     * Constructs a new InviteeOwnFunds class, setting the class_key column to ScheduledJobTableMap::CLASSKEY_12.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_12);
    }

    public function getQuery()
    {
        $query = DB::table('lender_invites as li')
            ->join('lenders AS l', 'l.id', '=', 'li.invitee_id')
            ->join('users AS u', 'u.id', '=', 'l.id')
            ->whereRaw("li.invitee_id IS NOT NULL")
            ->whereRaw("l.active = TRUE")
            ->whereRaw('u.joined_at <= \'' . Carbon::now()->subMonth() . '\'')
            ->whereRaw('u.joined_at >= \'' . Carbon::now()->subMonths(3)->subDays(7) . '\'')
            ->whereRaw('(
                    SELECT COUNT(*) FROM lender_invite_transactions lit
                    WHERE lit.lender_id = l.id
                    AND lit.type = ' . Transaction::LENDER_INVITE_INVITEE . '
            ) > 0
            ')
            ->whereRaw('(
                     SELECT COUNT(*) FROM loan_bids as lb
                     WHERE lb.lender_id = l.id
                     AND lb.active = TRUE
                     AND lb.is_lender_invite_credit = FALSE
            ) = 0
            ');

        return $this->joinQuery($query, 'u.id', 'u.joined_at');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $invitee = InviteQuery::create()
            ->getInvitee($user->getId());

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
        $lenderMailer->sendInviteeOwnFundsMail($user, $invitee);
    }
} // InviteeOwnFunds
