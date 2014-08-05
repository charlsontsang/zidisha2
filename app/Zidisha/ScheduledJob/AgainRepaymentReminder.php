<?php

namespace Zidisha\ScheduledJob;

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
class AgainRepaymentReminder extends ScheduledJobs
{

    /**
     * Constructs a new AgainRepaymentReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_7.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_7);
    }

    public function getQuery()
    {
        $dueDays = \Setting::get('loan.repaymentReminderDay');
        $dueAmount = \Setting::get('loan.repaymentDueAmount');

        return "
        SELECT rs.user_id, rs.loan_id, rs.due_date, rs.amount, rs.paid_amount 
        FROM installments as rs 
        JOIN borrowers AS br ON rs.borrower_id = br. ID
        WHERE rs.amount > 0  
        AND (
                (
                    (rs.amount-rs.paid_amount)> $dueAmount * (
                        SELECT
                            rate
                        FROM
                            exchange_rates
                        WHERE
                            start_date = (
                                SELECT
                                    MAX (start_date)
                                FROM
                                    exchange_rates
                                WHERE
                                    currency_code = (SELECT countries.currency_code FROM countries where countries.id = br.country_id)
                            )
                        )
                ) OR paid_amount IS NULL)
        AND `due_date`<=('" . Carbon::now()->subDays($dueDays) . "') 
        AND `due_date`>('" . Carbon::now()->subDays($dueDays + 1) . "')
        ";
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }
    
} // AgainRepaymentReminder
