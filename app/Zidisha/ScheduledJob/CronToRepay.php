<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\Vendor\SiftScience\SiftScienceService;


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
class CronToRepay extends ScheduledJob
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
            ->join('borrowers AS br', 'rs.borrower_id', '=', 'br.id')
            ->whereRaw("rs.amount > 0")
            ->whereRaw(
                "
                (
                        rs.paid_amount < (
                            rs.amount - " . $thresholdAmount . " * (
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
                                            currency_code = (
                                                SELECT
                                                    countries.currency_code
                                                FROM
                                                    countries
                                                WHERE
                                                    countries. ID = br.country_id
                                            )
                                    )
                            
                            AND exchange_rates.currency_code = (
                                SELECT
                                    countries.currency_code
                                FROM
                                    countries
                                WHERE
                                    countries. ID = br.country_id
                            )
                        )
                        
                    )
                    OR rs.paid_amount IS NULL
                )
                "
            )
            ->whereRaw('rs.due_date <= \'' . Carbon::now()->subDays(60) . '\'')
            ->whereRaw('rs.due_date > \'' . Carbon::now()->subDays(61) . '\'')
            ->orderBy('rs.borrower_id', 'asc');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $loanId = $this->getLoanId();

        /** @var  SiftScienceService $siftScienceService */
        $siftScienceService = \App::make('Zidisha\Vendor\SiftScience\SiftScienceService');

        $siftScienceService->loanArrearLabel($user, $loanId);

        $job->delete();
    }

} // CronToRepay
