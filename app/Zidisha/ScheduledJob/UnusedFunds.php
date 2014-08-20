<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Mail\LenderMailer;
use Zidisha\Notification\Notification;
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
class UnusedFunds extends ScheduledJob
{

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
        return DB::table('users AS u')
            ->selectRaw('u.id AS user_id, u.last_login_at as start_date')
            ->whereRaw("u.last_login_at < '" . Carbon::now()->subMonths(2) . "'")
            ->whereRaw('u.role = 0')
            ->whereRaw('u.active = true')
            ->whereRaw('(SELECT SUM(amount) FROM transactions t WHERE t.user_id = u.id) >= 50')
            ->whereRaw(
                '
                    (
                        SELECT COUNT(*) 
                        FROM notifications n
                        WHERE n.user_id = u.id
                        AND n.type = ' . Notification::UNUSED_FUNDS_NOTIFICATION . '
                    ) = 0
                '
            );
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
