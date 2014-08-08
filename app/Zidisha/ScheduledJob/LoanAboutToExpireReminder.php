<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Zidisha\Loan\Loan;
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
class LoanAboutToExpireReminder extends ScheduledJobs
{

    /**
     * Constructs a new LoanAboutToExpireReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_8.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_8);
    }


    public function getQuery()
    {
        $deadlineDays = \Setting::get('loan.deadline');
        $beforeDays = $deadlineDays - 3;
        $afterDays = $beforeDays + 1;

        return DB::table('loans AS u')
            ->selectRaw('u.id, borrower_id AS user_id, applied_at AS start_date, total_amount, summary, summary_translation')    
            ->whereRaw("status = " . Loan::OPEN)
            ->whereRaw("deleted_by_admin = false")
//            ->whereRaw("about_to_expire_notification = 0")
            ->whereRaw("applied_at <= '".Carbon::now()->subDays($beforeDays)."'")
            ->whereRaw("applied_at >= '".Carbon::now()->subDays($afterDays)."'");
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }
} // LoanAboutToExpireReminder
