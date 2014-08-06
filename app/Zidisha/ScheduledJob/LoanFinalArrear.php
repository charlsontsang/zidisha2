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
class LoanFinalArrear extends ScheduledJobs
{

    /**
     * Constructs a new LoanFinalArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_3.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_3);
    }


    public function getQuery()
    {
        return DB::table('installments AS rs')
            ->selectRaw(
                'rs.borrower_id AS user_id, rs.loan_id, rs.due_date AS start_date, rs.amount, rs.paid_amount, *'
            )
            ->join('borrowers AS br', 'rs.borrower_id', '=', 'br.id')
            ->whereRaw("rs.amount > 0")
            ->whereRaw(
                '(
                    rs.paid_amount IS NULL OR rs.paid_amount < (
                        rs.amount - 5 * (
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
                                AND exchange_rates.currency_code = (
                                        SELECT
                                            countries.currency_code
                                        FROM
                                            countries
                                        WHERE
                                            countries.id = br.country_id
                                    )
                        )
                    )
                )'
            )
            ->whereRaw('due_date <= \'' . Carbon::now()->subDays(14) . '\'')
            ->whereRaw('due_date > \'' . Carbon::now()->subDays(15) . '\'');

    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();
    }
} // LoanFinalArrear
