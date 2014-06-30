<?php

namespace Zidisha\Repayment;


use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;

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

    /**
     * @var array
     */
    protected $bids = [];
    
    protected $share = 0;

    public function __construct(Lender $lender)
    {
        $this->lender = $lender;
        $this->amount = $this->lenderInviteCredit = Money::create(0);
    }

    /**
     * @return \Zidisha\Lender\Lender
     */
    public function getLender()
    {
        return $this->lender;
    }
    
    public function addBid(Bid $bid)
    {
        if ($bid->getLenderInviteCredit()) {
            $this->lenderInviteCredit->add($bid->getAcceptedAmount());
        } else {
            $this->amount->add($bid->getAcceptedAmount());
        }
        
        return $this;
    }
    
    public function getTotalAcceptedAmount()
    {
        return $this->amount->add($this->lenderInviteCredit);
    }

    public function setShare($share)
    {
        $this->share = $share;
        
        return $this;
    }
    
    public function getAmount()
    {
        return $this->amount->multiply($this->share);
    }
    
    public function getLenderInviteCredit()
    {
        return $this->amount->multiply($this->share);
    }
}
