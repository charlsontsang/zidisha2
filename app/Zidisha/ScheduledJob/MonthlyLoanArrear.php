<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Borrower\ContactQuery;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\BorrowerMailer;
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
class MonthlyLoanArrear extends ScheduledJob
{

    /**
     * Constructs a new MonthlyLoanArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_4.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_4);
    }


    public function getQuery()
    {
        return DB::table('installments AS rs')
            ->selectRaw(
                'rs.borrower_id AS user_id, rs.loan_id, rs.due_date AS start_date, rs.amount, rs.paid_amount'
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
            ->whereRaw('due_date <= \'' . Carbon::now()->subDays(30) . '\'')
            ->whereRaw('due_date > \'' . Carbon::now()->subDays(31) . '\'');
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

        if (!$forgivenLoan) {
            /** @var  BorrowerMailer $borrowerMailer */
            $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');

            /** @var  BorrowerSmsService $borrowerSmsService */
            $borrowerSmsService = \App::make('Zidisha\Sms\BorrowerSmsService');

            $contacts = ContactQuery::create()
                ->filterByBorrower($borrower)
                ->find();

            foreach ($contacts as $contact) {
                $borrowerSmsService->sendLoanMonthlyArrearNotificationToContacts($contact, $loan);
            }

            $volunteerMentor = $borrower->getVolunteerMentor();

            if ($volunteerMentor) {
                $borrowerMailer->sendLoanMonthlyArrearToVolunteerMentor($volunteerMentor, $borrower, $loan);
            }

            $borrowerMailer->sendLoanMonthlyArrearMail($borrower, $loan);
            $borrowerSmsService->sendLoanMonthlyArrearNotification($borrower, $loan);
        }

        $job->delete();
    }
} // MonthlyLoanArrear
