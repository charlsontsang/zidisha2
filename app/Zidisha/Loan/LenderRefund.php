<?php

namespace Zidisha\Loan;


use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;

class LenderRefund {

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
        
    public function __construct($data)
    {
        $this->lender = $data['lender'];
        $this->amount  = $data['amount'];
        $this->lenderInviteCredit = $data['lenderInviteCredit'];
    }

    /**
     * @return \Zidisha\Lender\Lender
     */
    public function getLender()
    {
        return $this->lender;
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
