<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Currency\Money;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\Sms\BorrowerSmsService;


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
class AgainRepaymentReminder extends ScheduledJob
{

    /**
     * Constructs a new AgainRepaymentReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_7.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_7);
    }

    public function getQuery()
    {
        $dueDays = \Setting::get('loan.repaymentReminderDay');
        $dueAmount = \Setting::get('loan.repaymentDueAmount');

        return DB::table('installments as i')
            ->selectRaw('i.borrower_id AS user_id, i.loan_id AS loan_id, i.due_date AS start_date')
            ->join('borrowers AS br', 'i.borrower_id', '=', 'br.id')
            ->whereRaw("i.amount > 0")
            ->whereRaw(
                "(
                (
                    (i.amount - i.paid_amount) > (" . $dueAmount . "* (
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
                ) OR paid_amount IS NULL)"
            )
            ->whereRaw('i.due_date <= \'' . Carbon::now()->subDays($dueDays) . '\'')
            ->whereRaw('i.due_date > \'' . Carbon::now()->subDays($dueDays + 1) . '\'');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $borrower = $user->getBorrower();
        $loanId = $this->getLoanId();
        
        $loan = LoanQuery::create()
            ->findOneById($loanId);
        
        $forgivenLoan = ForgivenLoanQuery::create()
            ->findOneByLoanId($loanId);
        
        $installments = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->find();
        
        if (!$forgivenLoan && $installments) {
            /** @var  BorrowerMailer $borrowerMailer */
            $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');
            $borrowerMailer->sendAgainRepaymentReminder($borrower, $loan, $installments);

            /** @var  BorrowerSmsService $borrowerSmsService */
            $borrowerSmsService = \App::make('Zidisha\Sms\BorrowerSmsService');
            $borrowerSmsService->sendAgainRepaymentReminder($borrower, $loan, $installments);
        }
        
        $job->delete();
    }
} // AgainRepaymentReminder
