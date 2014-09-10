<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\ContactQuery;
use Zidisha\Borrower\Invite;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\ForgivenessLoanQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentSchedule;
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
class LoanFinalArrear extends ScheduledJob
{

    /**
     * Constructs a new LoanFinalArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_3.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_3);
    }


    public function getQuery()
    {
        $amountThreshold = \Setting::get('loan.repaymentDueAmount');

        $query = DB::table('installments AS i')
            ->join('borrowers AS br', 'i.borrower_id', '=', 'br.id')
            ->whereRaw("i.amount > 0")
            ->whereRaw(
                '(
                    i.paid_amount IS NULL OR i.paid_amount < (
                        i.amount - (' . $amountThreshold . ' * (
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
            ->whereRaw('due_date <= \'' . Carbon::now()->subDays(14) . '\'')
            ->whereRaw('due_date > \'' . Carbon::now()->subDays(15) . '\'');

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

        $dueInstallment = InstallmentQuery::create()
            ->getDueInstallment($loan);

        $calculator = new InstallmentCalculator($loan);
        $repaymentSchedule = RepaymentSchedule::createFromInstallments($loan, $calculator->generateLoanInstallments());

        $missedInstallmentCount = $repaymentSchedule->getMissedInstallmentCount();


        if (!$forgivenessLoan && $missedInstallmentCount < 2 && $dueInstallment->getDueDate() == $this->getStartDate()) {
            /** @var  BorrowerMailer $borrowerMailer */
            $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');

            /** @var  BorrowerSmsService $borrowerSmsService */
            $borrowerSmsService = \App::make('Zidisha\Sms\BorrowerSmsService');

            $borrowerMailer->sendLoanFinalArrearMail($borrower, $dueInstallment);
            $borrowerSmsService->sendLoanFinalArrearNotification($borrower, $dueInstallment);
        }

        $job->delete();
    }
} // LoanFinalArrear
