<?php

namespace Zidisha\Repayment;


use Zidisha\Currency\Money;

class RepaymentScheduleInstallmentPayment {

    private $amount;
    private $payment;

    public function __construct(InstallmentPayment $payment, Money $amount)
    {
        $this->amount = $amount;
        $this->payment = $payment;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getPayment()
    {
        return $this->payment;
    }
}
