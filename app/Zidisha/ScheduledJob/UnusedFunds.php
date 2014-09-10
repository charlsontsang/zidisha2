<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\User\User;


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
class UnusedFunds extends ScheduledJob
{
    const COUNT = 3;

    /**
     * Constructs a new UnusedFunds class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_6.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_6);
    }

    public function getQuery()
    {
        $query =  DB::table('users AS u')
            ->whereRaw("u.last_login_at < '" . Carbon::now()->subMonths(2) . "'")
            ->whereRaw('u.role = ' . User::LENDER_ROLE_ENUM)
            ->whereRaw('u.active = true')
            ->whereRaw('(SELECT SUM(amount) FROM transactions t WHERE t.user_id = u.id) >= 50');

        return $this->joinQuery($query, 'u.id', 'u.last_login_at');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $lender = $user->getLender();

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
        $lenderMailer->sendUnusedFundsNotification($lender);

        $job->delete();
    }
} // UnusedFunds
