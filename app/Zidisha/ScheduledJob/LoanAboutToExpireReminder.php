<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use Zidisha\Loan\Loan;
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
class LoanAboutToExpireReminder extends ScheduledJobs
{

    /**
     * Constructs a new LoanAboutToExpireReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_8.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_8);
    }


    public function getQuery()
    {
        $deadlineDays = \Setting::get('loan.deadline');
        $beforeDays = $deadlineDays - 3;
        $afterDays = $beforeDays + 1;
        
        return "SELECT id, borrower_id, applied_at, total_amount, summary, summary_translation
                FROM loans
                WHERE status = " . Loan::OPEN . "
                AND deleted_by_admin = false
                AND about_to_expire_notification = 0
                AND applied_at <= ".Carbon::now()->subDays($beforeDays)."
                AND applied_at >= ".Carbon::now()->subDays($afterDays);
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }
} // LoanAboutToExpireReminder
