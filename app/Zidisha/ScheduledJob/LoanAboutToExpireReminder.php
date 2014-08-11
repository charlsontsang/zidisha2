<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\LenderMailer;
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
class LoanAboutToExpireReminder extends ScheduledJobs
{

    /**
     * Constructs a new LoanAboutToExpireReminder class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_8.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_8);
    }


    public function getQuery()
    {
        $deadlineDays = \Setting::get('loan.deadline');
        $beforeDays = $deadlineDays - 3;
        $afterDays = $beforeDays + 1;

        return DB::table('loans AS u')
            ->selectRaw('u.id, borrower_id AS user_id, applied_at AS start_date, total_amount, summary, summary_translation')    
            ->whereRaw("status = " . Loan::OPEN)
            ->whereRaw("deleted_by_admin = false")
            ->whereRaw("applied_at <= '".Carbon::now()->subDays($beforeDays)."'")
            ->whereRaw("applied_at >= '".Carbon::now()->subDays($afterDays)."'");
    }

    public function process(Job $job)
    {
        $user= $this->getUser();
        $borrower = $user->getBorrower();
        $loan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->filterByStatus(Loan::OPEN)
            ->findOne();
        
        $bids = $loan->getBids();
        
        $totalRaisedAmount = $loan->getTotalAmount();
        $stillNeeded = $loan->getAmount()->subtract($totalRaisedAmount);

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');

        /** @var Bid $bid */
        if ($stillNeeded->greaterThan(Money::create(0, $loan->getCurrencyCode()))) {
            foreach ($bids as $bid) {
                $lenderMailer->sendLoanAboutToExpireMail($bid->getLender());
            }    
        }
        
        $job->delete();        
    }
} // LoanAboutToExpireReminder
