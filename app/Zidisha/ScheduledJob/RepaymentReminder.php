<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;


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
class RepaymentReminder extends ScheduledJobs
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
            ->selectRaw( 'borrower_id AS user_id, due_date AS start_date, *')
            ->whereRaw("amount > 0")
            ->whereRaw("(paid_amount IS NULL OR paid_amount < amount )")
            ->whereRaw("due_date >= '".Carbon::now()->addDay()."'")
            ->whereRaw("due_date<= '".Carbon::now()->addDays(2)."'");
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $borrower = $user->getBorrower();
        $loan = $borrower->getActiveLoan();
        
        $installment = InstallmentQuery::create()
            ->filterByLoan($loan)->filterByAmount(0, Criteria::GREATER_THAN)
            ->filterByPaidAmount($loan->getAmount(), Criteria::LESS_THAN)
            ->orderByDueDate('ASC')
            ->findOne();

        /** @var  BorrowerMailer $borrowerMailer */
        $borrowerMailer = \App::make('Zidisha\Mail\LenderMailer');
        
        if ($installment->getDueDate() == $this->getStartDate()) {
            if ($installment->getPaidAmount() > 0 && $installment->getPaidAmount() < $installment->getAmount()) {
                $dueDate = $installment->getDueDate();
                $paidAmount = $installment->getPaidAmount();
                $dueAmount = $installment->getAmount()->subtract($installment->getPaidAmount());

                $borrowerMailer->sendRepaymentReminder($borrower, $installment);                
            } else {

            }
        } elseif($installment->getDueDate() < $this->getStartDate()) {
            $amounts = InstallmentQuery::create()
                ->filterByLoan($loan)
                ->filterByDueDate(Carbon::create(), Criteria::LESS_EQUAL)
                ->select(array('amount_total', 'paid_amount_total'))
                ->withColumn('SUM(amount)', 'amount_total')
                ->withColumn('SUM(paid_amount)', 'paid_amount_total')
                ->find();            
        }
    }
} // RepaymentReminder
