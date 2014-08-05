<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Zidisha\Mail\BorrowerMailer;
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
class LoanFirstArrear extends ScheduledJobs
{

    /**
     * Constructs a new LoanFirstArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_2.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_2);
    }

    public function getQuery()
    {
//        return DB::table('users AS u');
        $query = "
        SELECT
            rs.borrower_id,
            rs.loan_id,
            rs.due_date,
            rs.amount,
            rs.paid_amount
        FROM
            installments AS rs
        JOIN borrowers AS br ON rs.borrower_id = br. ID
        WHERE
            rs.amount > 0
        AND (
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
                )
            )
        )
        AND rs.due_date <= ('".Carbon::now()->subDays(4)."')
        AND rs.due_date > ('".Carbon::now()->subDays(5)."')
        ";
        
        var_dump($query);
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();

        /** @var  BorrowerMailer $borrowerMailer */
        $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');
        $borrowerMailer->sendLoanFirstArrear($user);
    }
} // LoanFirstArrear
