<?php

namespace Zidisha\ScheduledJob;

use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\AutoLendingSetting;
use Zidisha\Lender\AutoLendingSettingQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
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
        } else {
            $totalAmountForAutomaticLending = $currentBalanceOfLender;
        }

        if ($totalAmountForAutomaticLending->greaterThan($maximumAllowedAmountForAutomaticLending)) {
            $numberOfLoans = floor($totalAmountForAutomaticLending->divide($maximumAllowedAmountForAutomaticLending)->getAmount());
            
            if ($numberOfLoans > 0 && $loansForLending) {
                foreach ($loansForLending as $loan) {
                    $data = [
                        'amount'             => $totalAmountForAutomaticLending,
                        'interestRate'       => $maximumInterestRate,
                        'isAutomatedLending' => true
                    ];
                    
                    $loanService->placeBid($loan, $lender, $data);
                }
            }
        }
    }
    
    private function getLoansForLending($loanPreference)
    {
        if ($loanPreference == AutoLendingSetting::HIGH_FEEDBCK_RATING) {
            $loans = LoanQuery::create()
                ->filterByStatus(Loan::OPEN)
                ->filterByLoanWithHighFeedbackComment()
                ->find();
            
            return $loans;
            
        } elseif ($loanPreference = AutoLendingSetting::EXPIRE_SOON) {
            $loans = LoanQuery::create()
                ->filterByAppliedAt()
                ->filterByStatus(Loan::OPEN)
                ->filterByAutoLendableLoan()
                ->orderByAppliedAt('DESC')
                ->find();
            
            return $loans;
            
        } elseif ($loanPreference == AutoLendingSetting::HIGH_NO_COMMENTS) {
            
            $loans = LoanQuery::create()
                ->filterByStatus(Loan::OPEN)
                ->filterByAutoLendableLoan()
                ->orderByAppliedAt('DESC')
                ->find();

            return $loans;
            
        } elseif ($loanPreference == AutoLendingSetting::LOAN_RANDOM) {
            $loans = LoanQuery::create()
                ->filterByStatus(Loan::OPEN)
                ->filterByAutoLendableLoan()
                ->orderByAppliedAt('ASC')
                ->find();

            $loans = shuffle ($loans);

            return $loans;
            
        } elseif ($loanPreference == AutoLendingSetting::AUTO_LEND_AS_PREV_LOAN) {
            $loans = LoanQuery::create()
                ->filterByStatus(Loan::OPEN)
                ->filterByAutoLendableLoan()
                ->find();
            
            return $loans;
        }
        return null;
    }
} // AutomatedLending
