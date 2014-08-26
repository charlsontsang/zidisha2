<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Loan\Loan;
use Zidisha\Repayment\RepaymentSchedule;

class RescheduleCalculator {

    /**
     * @var \Zidisha\Repayment\RepaymentSchedule
     */
    private $repaymentSchedule;

    public function __construct(Loan $loan, RepaymentSchedule $repaymentSchedule)
    {
        $this->loan = $loan;
        $this->repaymentSchedule = $repaymentSchedule;
    }

    public function minInstallmentAmount()
    {
        // TODO
    }
    
}
