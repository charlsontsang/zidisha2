<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
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
class CronToRepay extends ScheduledJobs
{

    /**
     * Constructs a new CronToRepay class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_10.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_10);
    }

    public function getQuery()
    {
        $thresholdAmount = \Config::get('constants.repaymentAmountThreshold');

        return DB::table('installments as rs')
            ->selectRaw('rs.borrower_id AS user_id, rs.loan_id, rs.due_date AS start_date, rs.amount, rs.paid_amount')
//            ->orderByRaw('rs.borrower_id asc')
            ->join('borrowers AS br', 'rs.borrower_id', '=', 'br.ID')
            ->whereRaw("rs.amount > 0")
            ->whereRaw("
                        ( 
                            rs.paid_amount < ( 
                                    rs.amount - $thresholdAmount * ( 
                                            SELECT rate 
                                            FROM exchange_rates 
                                            WHERE start_date = ( 
                                                    SELECT MAX (start_date) 
                                                    FROM exchange_rates 
                                                    WHERE currency_code = (
                                                        SELECT countries.currency_code 
                                                        FROM countries 
                                                        where countries.id = br.country_id
                                                    ) 
                                            )
                                    AND exchange_rates.currency_code = (
                                        SELECT
                                            countries.currency_code
                                        FROM
                                            countries
                                        WHERE
                                            countries.id = br.country_id
                                    )
                            ) 
                            OR paid_amount IS NULL
                        )
                ")
            ->whereRaw('rs.due_date <= \'' . Carbon::now()->subDays(60) . '\'')
            ->whereRaw('rs.due_date > \'' . Carbon::now()->subDays(61) . '\'');
    }

    public function process(Job $job)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }

} // CronToRepay
