<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Loan\ForgivenessLoanQuery;
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

        $query = DB::table('installments as i')
            ->join('borrowers AS br', 'i.borrower_id', '=', 'br.id')
            ->whereRaw("i.amount > 0")
            ->whereRaw(
                "
                (
                        i.paid_amount IS NULL OR i.paid_amount < (
                        i.amount - (" . $thresholdAmount . " * (
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
                    )
                )
                "
            )
            ->whereRaw('i.due_date <= \'' . Carbon::now()->subDays(60) . '\'')
            ->whereRaw('i.due_date > \'' . Carbon::now()->subDays(61) . '\'')
            ->orderBy('i.borrower_id', 'asc');

        return $this->joinQuery($query, 'i.borrower_id', 'i.due_date', 'i.loan_id');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $loanId = $this->getLoanId();

        $forgivenessLoan = ForgivenessLoanQuery::create()
            ->findOneByLoanId($loanId);

        if (!$forgivenessLoan) {
            /** @var  SiftScienceService $siftScienceService */
            $siftScienceService = \App::make('Zidisha\Vendor\SiftScience\SiftScienceService');

            $siftScienceService->loanArrearLabel($user, $loanId);
        }

        $job->delete();
    }

} // CronToRepay
