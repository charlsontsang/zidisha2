<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\ContactQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\InstallmentPayment;
use Zidisha\Repayment\InstallmentPaymentQuery;
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
class LoanFirstArrear extends ScheduledJob
{

    /**
     * Constructs a new LoanFirstArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_2.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_2);
    }

    public function getQuery()
    {
        return DB::table('installments AS rs')
            ->selectRaw(
                'rs.borrower_id AS user_id, rs.due_date AS start_date, rs.loan_id AS loan_id'
            )
            ->join('borrowers AS br', 'rs.borrower_id', '=', 'br.id')
            ->whereRaw("rs.amount > 0")
            ->whereRaw(
                '(
                    rs.paid_amount IS NULL OR rs.paid_amount < (
                        rs.amount - (5 * (
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
                )'
            )
            ->whereRaw('rs.due_date <= \'' . Carbon::now()->subDays(4) . '\'')
            ->whereRaw('rs.due_date > \'' . Carbon::now()->subDays(5) . '\'');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $borrower = $user->getBorrower();

        $loanId = $this->getLoanId();

        $loan = LoanQuery::create()
            ->findOneById($loanId);

        $installment = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->filterByAmount(0, Criteria::GREATER_THAN)
            ->filterByPaidAmount($loan->getAmount()->getAmount(), Criteria::LESS_THAN)
            ->orderByDueDate('ASC')
            ->findOne();

        if ($installment->getDueDate() == $this->getStartDate()) {
            //Check if this is the borrowers first missed installment.                
            if ($installment) {
                /** @var  BorrowerMailer $borrowerMailer */
                $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');

                /** @var  BorrowerSmsService $borrowerSmsService */
                $borrowerSmsService = \App::make('Zidisha\Sms\BorrowerSmsService');

                $borrowerMailer->sendLoanFirstArrearMail($borrower, $loan);
                $borrowerSmsService->sendLoanFirstArrearNotification($borrower, $loan);
            }
        }

        $job->delete();

    }
} // LoanFirstArrear
