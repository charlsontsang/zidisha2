<?php

namespace Zidisha\Generate;


use Carbon\Carbon;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;

class BidGenerator extends Generator
{
    /**
     * @var Lender
     */
    protected $lender;

    /**
     * @var Loan
     */
    protected $loan;
    
    protected $bidAtStartDate;
    protected $bidAtEndDate;
    
    protected $minAmount = 5;
    protected $maxAmount = 20;

    protected $minInterestRate = 0;
    protected $maxInterestRate = 15;
    
    protected $isLenderInviteCredit = false;
    protected $isAutomatedLending = false;
    
    /**
     * @var \Zidisha\Loan\LoanService
     */
    private $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function setLender(Lender $lender)
    {
        $this->lender = $lender;
        
        return $this;
    }

    public function setLoan(Loan $loan)
    {
        $this->loan = $loan;
        
        return $this;
    }

    public function bidAtBetween($startDate = '- 16 months', $endDate = 'now')
    {
        $this->bidAtStartDate = $startDate;
        $this->bidAtEndDate = $endDate;

        return $this;
    }

    public function amountBetween($min = 50, $max = 400)
    {
        $this->minAmount = $min;
        $this->maxAmount = $max;

        return $this;
    }

    public function amount($amount)
    {
        $this->amountBetween($amount, $amount);

        return $this;
    }

    public function interestRateBetween($min = 0, $max = 15)
    {
        $this->minInterestRate = $min;
        $this->maxInterestRate = $max;

        return $this;
    }

    public function interestRate($amount)
    {
        $this->interestRateBetween($amount, $amount);
        
        return $this;
    }

    public function setIsLenderInviteCredit($isLenderInviteCredit = false)
    {
        $this->isLenderInviteCredit = $isLenderInviteCredit;
    }

    public function setIsAutomatedLending($isAutomatedLending = false)
    {
        $this->isAutomatedLending = $isAutomatedLending;
    }

    protected function beforeGenerate()
    {
        if (!$this->lender) {
            throw new \Exception("No lender");
        }

        if (!$this->loan) {
            throw new \Exception("No loan");
        }
    }

    protected function doGenerate($i)
    {
        if ($this->bidAtStartDate) {
            $bidDate = $this->faker->dateTimeBetween($this->bidAtStartDate, $this->bidAtEndDate);
        } else {
            $bidDate = Carbon::instance($this->loan->getAppliedAt());
            $bidDate->addDays($this->faker->numberBetween(1, 15));   
        }

        $data = [
            'bidAt'                  => $bidDate,
            'amount'                 => $this->faker->numberBetween($this->minAmount, $this->maxAmount),
            'interestRate'           => $this->faker->numberBetween($this->minInterestRate, $this->maxInterestRate),
            'isLenderInviteInterest' => $this->isLenderInviteCredit,
            'isAutomatedLending'     => $this->isAutomatedLending,
        ];
        
        return $this->loanService->placeBid($this->loan, $this->lender, $data);
    }
    
    public function fullyFund($lenders)
    {
        $generator = clone $this;
        $bids = [];

        if (!$this->loan) {
            throw new \Exception("No loan");
        }
        
        while ($this->loan->getRaisedPercentage() < 100) {
            $generator->setLender($this->faker->randomElement($lenders));
            $bids[] = $generator->generateOne();
        }
        
        return $bids;
    }
}
