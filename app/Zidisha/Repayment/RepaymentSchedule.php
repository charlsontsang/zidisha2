<?php

namespace Zidisha\Repayment;


use Traversable;

class RepaymentSchedule implements \IteratorAggregate {

    private $installments = [];

    public function __construct($installments)
    {
        $this->installments = $installments;
    }

    public function getInstallments()
    {
        return $this->installments;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->installments);
    }
}