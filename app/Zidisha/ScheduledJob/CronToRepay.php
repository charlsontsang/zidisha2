<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
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
class CronToRepay extends ScheduledJobs
{

    /**
     * Constructs a new CronToRepay class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_10.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_10);
    }

    public function getQuery()
    {
        $thresholdAmount = \Config::get('constants.repaymentAmountThreshold');
        return "
        SELECT rs.borrower_id, rs.loan_id, rs.due_date, rs.amount, rs.paid_amount
        FROM installments as rs 
        join borrowers as br on rs.borrower_id=br.id
        WHERE rs.amount > 0  
        AND (
                rs.paid_amount IS NULL 
                    OR 
                rs.paidamt < ( 
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
                        ) 
                )
        )
        AND rs.due_date <= (" . Carbon::now()->subDays(60) . ") 
        AND rs.due_date >(" . Carbon::now()->subDays(61) . ") 
        order by rs.borrower_id asc
        ";
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }

} // CronToRepay
