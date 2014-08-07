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
class RepaymentReminder extends ScheduledJob
{

    /**
     * Constructs a new RepaymentReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_5.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_5);
    }

    public function getQuery()
    {
        return DB::table('installments')
            ->selectRaw( ' borrower_id AS user_id, due_date AS start_date, *')
            ->whereRaw("amount > 0")
            ->whereRaw("(paid_amount IS NULL OR paid_amount < amount )")
            ->whereRaw("due_date >= '".Carbon::now()->addDay()."'")
            ->whereRaw("due_date<= '".Carbon::now()->addDays(2)."'");
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }
} // RepaymentReminder
