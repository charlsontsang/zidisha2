<?php

namespace Zidisha\Payment;

use Zidisha\Payment\Map\PaymentTableMap;


/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'payments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class BidPayment extends Payment
{

    /**
     * Constructs a new BidPayment class, setting the class_key column to PaymentTableMap::CLASSKEY_2.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(PaymentTableMap::CLASSKEY_2);
    }

} // BidPayment
