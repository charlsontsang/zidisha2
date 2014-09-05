<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Currency\Money;
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
class RepaymentReminder extends ScheduledJob
{

    /**
     * Constructs a new RepaymentReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_5.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_5);
    }

    public function getQuery()
    {
        return DB::table('installments')
            ->selectRaw('borrower_id AS user_id, due_date AS start_date, installments.loan_id AS loan_id')
            ->whereRaw("amount > 0")
            ->whereRaw("(paid_amount IS NULL OR paid_amount < amount )")
            ->whereRaw("due_date >= '" . Carbon::now()->addDay() . "'")
            ->whereRaw("due_date <='" . Carbon::now()->addDays(2) . "'"); 
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $borrower = $user->getBorrower();
        $loan = $borrower->getActiveLoan();
        
        $installment = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->where('Installment.Amount > 0')
            ->where('Installment.PaidAmount IS NULL OR Installment.PaidAmount < Installment.Amount')
            ->orderByDueDate('ASC')
            ->findOne();

        /** @var  BorrowerMailer $borrowerMailer */
        $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');

        /** @var  BorrowerSmsService $borrowerSmsService */
        $borrowerSmsService = \App::make('Zidisha\Sms\BorrowerSmsService');

        if ($installment  && $installment->getDueDate() == $this->getStartDate()) {
            if ($installment->getPaidAmount()->isPositive() && $installment->getPaidAmount()->lessThan($installment->getAmount())) {
                $borrowerMailer->sendRepaymentReminderTomorrow($borrower, $installment);
                $borrowerSmsService->sendRepaymentReminderTomorrow($borrower, $installment);
            } else {
                $borrowerMailer->sendRepaymentReminder($borrower, $installment);
                $borrowerSmsService->sendRepaymentReminder($borrower, $installment);
            }
        } elseif ($installment  && $installment->getDueDate() < $this->getStartDate()) {
            $amounts = InstallmentQuery::create()
                ->filterByLoan($loan)
                ->filterByDueDate(Carbon::now(), Criteria::LESS_EQUAL)
                ->select(array('amount_total', 'paid_amount_total'))
                ->withColumn('SUM(amount)', 'amount_total')
                ->withColumn('SUM(paid_amount)', 'paid_amount_total')
                ->find();

            //Send mail to borrower
            $borrowerMailer->sendRepaymentReminderForDueAmount($borrower, $loan, $amounts);
            $borrowerSmsService->sendRepaymentReminderForDueAmount($borrower, $loan, $amounts);
        }

        $job->delete();
    }
} // RepaymentReminder
