<?php

namespace Zidisha\ScheduledJob;

use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\AutoLendingSettingQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\LoanService;
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
class AutomatedLending extends ScheduledJob
{

    /**
     * Constructs a new AutomatedLending class, setting the class_key column to ScheduledJobTableMap::CLASSKEY_11.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_11);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery()
    {
        return DB::table('auto_lending_settings as s')
            ->selectRaw('s.lender_id AS user_id, COALESCE(s.last_processed, s.created_at) AS start_date')
            ->whereRaw('s.preference = 1');
    }

    public function process(Job $job)
    {
        /** @var LoanService $loanService */
        $loanService = \App::make('Zidisha\Loan\LoanService');
        
        $userId = $this->getUserId();
        $lender = LenderQuery::create()
            ->findOneById($userId);

        $autoLendingSetting = AutoLendingSettingQuery::create()
            ->findOneByLenderId($lender->getId());

        $currentBalanceOfLender = TransactionQuery::create()
            ->getCurrentBalance($lender->getId());
        
        $maximumAllowedAmountForAutomaticLending = Money::create(\Config::get('constants.autoLendAmount'), 'USD');

        $loanPreference = $autoLendingSetting->getPreference();
        $minimumInterestRate = $autoLendingSetting->getMinDesiredInterest();
        $maximumInterestRate = $autoLendingSetting->getMaxDesiredInterest();
        
        
        $loansForLending = $this->getLoansForLending($loanPreference);
        
        
        if($autoLendingSetting->getCurrentAllocated() == 0 ) {
            $totalAmountForAutomaticLending = $currentBalanceOfLender->subtract($autoLendingSetting->getLenderCredit());

            if ($totalAmountForAutomaticLending->greaterThan($maximumAllowedAmountForAutomaticLending)) {
                $numberOfLoans = floor($totalAmountForAutomaticLending->divide($maximumAllowedAmountForAutomaticLending)->getAmount());
                
                if ($numberOfLoans > 0) {
                    for ($i = 0; $i <= $numberOfLoans; $i++) {
//                        $data = [
//                            'amount'       => $totalAmountForAutomaticLending,
//                            'interestRate' => 34,
//                        ];
                    }
                }
            }

        } else {
            $totalAmountForAutomaticLending = $currentBalanceOfLender;
            if ($totalAmountForAutomaticLending->greaterThan($maximumAllowedAmountForAutomaticLending)) {
                $numberOfLoans = floor($totalAmountForAutomaticLending->divide($maximumAllowedAmountForAutomaticLending)->getAmount());
                
                if ($numberOfLoans > 0) {
                    for ($i = 0; $i <= $numberOfLoans; $i++) {

                    }
                }
            }
        }
        
    }
} // AutomatedLending
