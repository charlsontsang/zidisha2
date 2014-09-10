<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Currency\Money;
use Zidisha\Loan\ForgivenessLoanQuery;
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

        $query = DB::table('installments as i')
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

        return $this->joinQuery($query, 'i.borrower_id', 'i.due_date', 'i.loan_id');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $borrower = $user->getBorrower();
        $loanId = $this->getLoanId();

        $loan = LoanQuery::create()
            ->findOneById($loanId);

        $forgivenessLoan = ForgivenessLoanQuery::create()
            ->findOneByLoanId($loanId);

        $installment = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->where('Installment.Amount > 0')
            ->where('(Installment.PaidAmount IS NULL OR Installment.PaidAmount < Installment.Amount)')
            ->orderByDueDate('ASC')
            ->findOne();

        if (!$forgivenessLoan && $installment) {
            $amounts = InstallmentQuery::create()
                ->filterByLoan($loan)
                ->filterByDueDate(Carbon::now(), Criteria::LESS_EQUAL)
                ->select(array('amount_total', 'paid_amount_total'))
                ->withColumn('SUM(amount)', 'amount_total')
                ->withColumn('SUM(paid_amount)', 'paid_amount_total')
                ->findOne();
            $dueAmount = Money::create(
                ($amounts['amount_total'] - $amounts['paid_amount_total']),
                $borrower->getCountry()->getCurrencyCode()
            );

            /** @var  BorrowerMailer $borrowerMailer */
            $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');
            $borrowerMailer->sendAgainRepaymentReminder($borrower, $installment, $dueAmount);
        }

        $job->delete();
    }
} // AgainRepaymentReminder
