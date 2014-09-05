<?php

namespace Zidisha\Borrower\Calculator;


use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Credit\CreditsEarnedQuery;
use Zidisha\Credit\CreditSetting;
use Zidisha\Credit\CreditSettingQuery;
use Zidisha\Currency\Converter;
use Zidisha\Currency\Currency;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\Money;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentService;

class CreditLimitCalculator
{
    /**
     * @var Borrower
     */
    protected $borrower;

    /**
     * @var \Zidisha\Currency\ExchangeRate
     */
    protected $exchangeRate;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var LoanService
     */
    protected $loanService;

    /**
     * @var RepaymentService
     */
    protected $repaymentService;
    
    /**
     * @var Money
     */
    protected $inviteCredit;

    /**
     * @var Money
     */
    protected $VMCredit;
    
    /**
     * @var Money
     */
    protected $bonusCredit;

    /**
     * @var Money
     */
    protected $commentCredit;

    /**
     * @var Money
     */
    protected $baseCreditLimit;
    
    /**
     * @var Money
     */
    protected $creditLimit;

    /**
     * @var integer
     */
    protected $minLoanLength;

    /**
     * @var bool
     */
    protected $repaidLate = false;
    
    /**
     * @var bool
     */
    protected $isFirstLoan = false;
    
    /**
     * @var bool
     */
    protected $insufficientRepaymentRate = false;
    
    /**
     * @var bool
     */
    protected $insufficientLoanLength = false;

    /**
     * @var bool
     */
    protected $repaidTooEarly = false;
    
    /**
     * @var integer
     */
    protected $sufficientRepaymentRate;

    public function __construct(Borrower $borrower, ExchangeRate $exchangeRate)
    {
        $this->borrower = $borrower;
        $this->exchangeRate = $exchangeRate;

        $this->currency = $borrower->getCountry()->getCurrency();
        
        $this->loanService = \App::make('Zidisha\Loan\LoanService');
        $this->repaymentService = \App::make('Zidisha\Repayment\RepaymentService');
        
        $this->getBaseCreditLimit();
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Bonus credit for inviting new members.
     * 
     * We give bonus credit for each invited member, as long as that member maintains
     * a invite.minRepaymentRate on-time monthly installment repayment rate.
     * 
     * @return Money
     */
    public function getInviteCredit()
    {
        if ($this->inviteCredit === null) {            
            $invitees= InviteQuery::create()
                ->joinWith('Invitee')
                ->joinWith('Invitee.Country')
                ->filterByBorrower($this->borrower)
                ->filterByInviteeId(null, Criteria::NOT_EQUAL)
                ->find();
            
            $minRepaymentRate = Setting::get('invite.minRepaymentRate');
            $creditEarned = Money::create(0, $this->currency);
            $credit = CreditSettingQuery::create()
                ->getBorrowerInviteCreditAmount($this->borrower->getCountry());
            
            foreach ($invitees as $invitee) {
                $lastLoanOfInvitee = LoanQuery::create()
                    ->findLastLoan($invitee->getInvitee());
                if (!$lastLoanOfInvitee) {
                    continue;
                }
                $repaymentRate = $this->loanService->getOnTimeRepaymentScore($invitee->getInvitee());
                if ($repaymentRate >= $minRepaymentRate) {
                    $creditEarned = $creditEarned->add($credit);
                }
            }

            $this->inviteCredit = $creditEarned;
        }
        
        return $this->inviteCredit;
    }

    /**
     * Bonus credit for volunteer mentor assigned members who are current with repayments.
     * 
     * We give bonus credit for every assigned member with an active loan and no installments overdue.
     * 
     * @return Money
     */
    public function getVMCredit()
    {
        if ($this->VMCredit === null) {
            $creditEarned = Money::create(0, $this->currency);

            if ($this->borrower->getUser()->isVolunteerMentor()) {
                $assignedMembers = BorrowerQuery::create()
                    ->joinWith('ActiveLoan')
                    ->filterByVolunteerMentorId($this->borrower->getId())
                    ->find();
                
                $borrowerInviteCredit = CreditSettingQuery::create()
                    ->getBorrowerInviteCreditAmount($this->borrower->getCountry());

                foreach ($assignedMembers as $assignedMember) {
                    if ($assignedMember->getLoanStatus() == Loan::ACTIVE) {
                        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($assignedMember->getActiveLoan());
                        
                        if ($repaymentSchedule->getOverDueInstallmentCount() == 0) {
                            $creditEarned = $creditEarned->add($borrowerInviteCredit);
                        }
                    }
                }
            }

            $this->VMCredit = $creditEarned;
        }
        
        return $this->VMCredit;
    }

    public function getCommentCredit()
    {
        if ($this->commentCredit === null) {
            $total = CreditsEarnedQuery::create()
                ->filterByBorrower($this->borrower)
                ->filterByCreditType(CreditSetting::COMMENT_CREDIT)
                ->filterByLoanId($this->borrower->getActiveLoanId())
                ->select(array('total'))
                ->withColumn('SUM(credit)', 'total')
                ->findOne();
            
            $this->commentCredit = Money::create($total ?: 0, $this->currency);
        }

        return $this->commentCredit;
    }

    /**
     * @return Money
     */
    public function getMaximumBonusCredit()
    {
        return Converter::fromUSD(Money::create(1000, 'USD'), $this->currency, $this->exchangeRate);
    }

    /**
     * @return Money
     */
    public function getBonusCredit()
    {
        if ($this->bonusCredit === null) {
            $creditEarned = $this->getInviteCredit()->add($this->getVMCredit());
            $this->bonusCredit = $creditEarned->min($this->getMaximumBonusCredit());   
        }
        
        return $this->bonusCredit;
    }
    
    /**
     * @return Money
     */
    public function getTotalBonusCredit()
    {
        return $this->getInviteCredit()->add($this->getVMCredit());
    }

    /**
     * @return integer
     */
    public function getMinimumRepaymentRate()
    {
        return Setting::get('invite.minRepaymentRate');
    }

    protected function calculateCreditLimit()
    {
        $loanStatus = $this->borrower->getLoanStatus();
        
        if ($loanStatus == Loan::ACTIVE || $loanStatus == Loan::FUNDED) {
            // we calculate credit limit based on current loan amount
            $loan = $this->borrower->getActiveLoan();
            //assume current loan will be repaid on time for purpose of displaying future credit limits
            $onTime = true;
        } else {
            // we calculate credit limit based on most recently repaid loan amount
            $loan = LoanQuery::create()->findLastCompletedLoan($this->borrower);
            $onTime = $loan ? $this->loanService->isRepaidOnTime($this->borrower, $loan) : true;
        }
        
        if ($loan) {
            $loanAmount = $loan->isDisbursed() ? $loan->getDisbursedAmount() : $loan->getAmount();
        } else {
            // no fundraising or completed loans
            $loanAmount = Money::create(0, $this->currency);
        }

        $loanUsdAmount = Converter::toUSD($loanAmount, $this->exchangeRate);

        // TODO extract for reuse
        if ($loanUsdAmount->lessThanOrEqual(Money::create(200, 'USD'))) {
            $timeThreshold = Setting::get('loan.loanIncreaseThresholdLow');
            $percentIncrease = Setting::get('loan.secondLoanPercentage');
        } elseif ($loanUsdAmount->lessThanOrEqual(Money::create(1000, 'USD'))) {
            $timeThreshold = Setting::get('loan.loanIncreaseThresholdMid');
            $percentIncrease = Setting::get('loan.nextLoanPercentage');
        } elseif ($loanUsdAmount->lessThanOrEqual(Money::create(3000, 'USD'))) {
            $timeThreshold = Setting::get('loan.loanIncreaseThresholdHigh');
            $percentIncrease = Setting::get('loan.nextLoanPercentage');
        } else {
            $timeThreshold = Setting::get('loan.loanIncreaseThresholdTop');
            $percentIncrease = Setting::get('loan.nextLoanPercentage');
        }
        $this->minLoanLength = $timeThreshold;

        $firstLoanMaxUsdAmount = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $firstLoanMaxAmount = Converter::fromUSD($firstLoanMaxUsdAmount, $this->currency, $this->exchangeRate);
        
        $hasDisbursedLoan = LoanQuery::create()->hasDisbursedLoan($this->borrower);
        $this->isFirstLoan = !$hasDisbursedLoan;

        // case where borrower has not yet received first loan disbursement
        // credit limit should equal admin first loan size plus bonus credit if applicable
        if (!$hasDisbursedLoan) {
            $isInvited = InviteQuery::create()
                ->filterByInvitee($this->borrower)
                ->count();
            
            if ($loan && $loan->getUsdAmount()->greaterThan($firstLoanMaxUsdAmount)) {
                return $loanAmount;
            }

            // add bonus for new members who were invited by eligible existing members
            $bonusCredit = Money::create($isInvited ? 100 : 0, 'USD') ;
            $totalUsdAmount = $firstLoanMaxUsdAmount->add($bonusCredit);
            
            return Converter::fromUSD($totalUsdAmount, $this->currency, $this->exchangeRate);
        }

        // case where last completed loan was repaid late
        // credit limit should equal maximum amount of the repaid on time loans
        // or admin first loan setting (if no loan was ever repaid on time)   
        if (!$onTime) {
            $this->repaidLate = true;
            $amount = LoanQuery::create()
                ->filterById($loan->getId(), Criteria::NOT_EQUAL)
                ->getMaximumRepaidDisbursedAmount($this->borrower, $this->currency);

            $amountUsd = Converter::toUSD($amount, $this->exchangeRate);

            if ($amountUsd->greaterThan(Money::create(1, 'USD'))) {
                $previousLoanAmount = $amountUsd;
            } else {
                if ($loan->getUsdAmount()->greaterThan($firstLoanMaxUsdAmount)) {
                    $previousLoanAmount = $loan->getDisbursedAmount();
                } else {
                    $previousLoanAmount = $firstLoanMaxAmount;
                }
            }
        
            if ($previousLoanAmount->greaterThan(Money::create(10, $this->currency))) {
                return $previousLoanAmount;
            }
            
            return $firstLoanMaxAmount;
        }
        
        // case where last loan was repaid on time
        // we next check whether monthly installment repayment rate meets threshold
        $repaymentRate = $this->loanService->getOnTimeRepaymentScore($this->borrower);
        $minRepaymentRate = $this->getMinimumRepaymentRate();

        // case where last loan repaid on time but monthly installment repayment rate is below admin threshold
        // loan size stays same
        if ($repaymentRate < $minRepaymentRate) {
            $this->insufficientRepaymentRate = true;
            return $loanAmount;
        }

        $this->sufficientRepaymentRate = $repaymentRate;
        
        // case where last loan repaid on time and overall repayment is above admin threshold
        // we next check whether the last loan was held long enough to qualify for credit limit increase,
        // with the amount of time loans need to be held and size of increase both dependent on previous loan amount
        if ($loan->isDisbursed()) {
            $currentTime = Carbon::now();
            $disbursedAt = Carbon::instance($loan->getDisbursedAt());
            $months = $disbursedAt->diffInMonths($currentTime);
        } else {
            $months = 0;
        }
        
        // if the loan has not been held long enough then borrower does not yet qualify for credit limit increase
        if ($months < $timeThreshold) {
            $this->insufficientLoanLength = true;
            return $loanAmount;
        }
        
        // case where last loan was repaid on time, overall repayment rate is above threshold
        // and loan held for long enough to qualify for credit limit increase
        $lastInstallmentAmount = InstallmentQuery::create()
            ->getLastInstallmentAmount($loan);
        
        $lastInstallmentUsdAmount = Converter::toUSD($lastInstallmentAmount, $this->exchangeRate);

        // case where more than 10% and $100 of last loan was paid in the last installment
        if ($lastInstallmentAmount->greaterThan($loanAmount->multiply(0.1))
            && $lastInstallmentUsdAmount->greaterThan(Money::create(100, 'USD')))
        {
            $this->repaidTooEarly;
            return $loanAmount;
        }
        
        return $loanAmount->multiply($percentIncrease)->divide(100);
    }

    public function getBaseCreditLimit()
    {
        if ($this->baseCreditLimit === null) {
            $this->baseCreditLimit = $this->calculateCreditLimit()->ceil();
        }

        return $this->baseCreditLimit;
    }

    public function getCreditLimit()
    {
        if ($this->creditLimit === null) {
            $this->creditLimit = $this->getBaseCreditLimit()->add($this->getBonusCredit());
        }
        
        return $this->creditLimit;
    }

    /**
     * @return int
     */
    public function getMinLoanLength()
    {
        return $this->minLoanLength;
    }

    /**
     * @return boolean
     */
    public function hasRepaidLate()
    {
        return $this->repaidLate;
    }

    /**
     * @return boolean
     */
    public function isFirstLoan()
    {
        return $this->isFirstLoan;
    }

    /**
     * @return boolean
     */
    public function hasInsufficientRepaymentRate()
    {
       return $this->insufficientRepaymentRate;
    }

    /**
     * @return boolean
     */
    public function hasInsufficientLoanLength()
    {
        return $this->insufficientLoanLength;
    }

    /**
     * @return mixed
     */
    public function hasRepaidTooEarly()
    {
        return $this->repaidTooEarly;
    }

    /**
     * @return mixed
     */
    public function getSufficientRepaymentRate()
    {
        return $this->sufficientRepaymentRate;
    }

}
