<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Zidisha\ScheduledJob\Map\ScheduledJobsTableMap;


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
class UnusedFunds extends ScheduledJobs
{

    /**
     * Constructs a new UnusedFunds class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_6.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_6);
    }

    public function getQuery()
    {
        return DB::table('users AS u')
            ->whereRaw("u.last_login_at < '". Carbon::now()->subMonths(2) ."'")
            ->whereRaw('u.role = 0')
            ->whereRaw('(SELECT SUM(amount) FROM transactions t
                     WHERE t.user_id = u.id) >= 50');
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $lender = $scheduleJobs->getUser()->getLender();

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
        $lenderMailer->sendUnusedFundsNotification($lender);
    }
} // UnusedFunds
