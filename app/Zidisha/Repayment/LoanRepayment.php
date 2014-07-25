<?php

namespace Zidisha\Repayment;


use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;

class LoanRepayment {

    /**
     * @var Lender
     */
    protected $lender;

    /**
     * @var Money
     */
    protected $amount;
    
    /**
     * @var Money
     */
    protected $lenderInviteCredit;
        
    public function __construct(Lender $lender)
    {
        $this->lender = $lender;
        $this->amount  = Money::create(0);
        $this->lenderInviteCredit = Money::create(0);
    }

    /**
     * @return \Zidisha\Lender\Lender
     */
    public function getLender()
    {
        return $this->lender;
    }
    
    public function addRepaidAmount(Money $repaidAmount, $lenderInviteCredit)
    {
        if ($lenderInviteCredit) {
            $this->lenderInviteCredit = $this->lenderInviteCredit->add($repaidAmount);
        } else {
            $this->amount = $this->amount->add($repaidAmount);
        }
        
        return $this;
    }
    
    public function getTotalAmount()
    {
        return $this->amount->add($this->lenderInviteCredit);
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getLenderInviteCredit()
    {
        return $this->lenderInviteCredit;
    }
}
