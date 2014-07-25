<?php
namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;

class RefundLender
{

    protected $amount;
    protected $lender;

    public function __construct($refund)
    {
        $this->amount = $refund['amount'];
        $this->lender = $refund['lender'];
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Lender
     */
    public function getLender()
    {
        return $this->lender;
    }
} 
