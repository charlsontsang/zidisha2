<?php

namespace Zidisha\Repayment;


use Zidisha\Currency\Money;

class RepaymentScheduleInstallment {

    private $payments = [];
    private $installment;

    public function __construct(Installment $installment, $payments)
    {
        $this->installment = $installment;
        $this->payments = $payments;
    }

    public function getPayments()
    {
        return $this->payments;
    }

    public function getInstallment()
    {
        return $this->installment;
    }
}