<?php

namespace Zidisha\Repayment;

use Zidisha\Borrower\Borrower;

class RepaymentService {

    public function addBorrowerPayment(Borrower $borrower, $data)
    {
        $borrowerPayment = new BorrowerPayment();
        $borrowerPayment->setBorrower($borrower);
        $borrowerPayment->setCountryCode($data['country_code']);
        $borrowerPayment->setReceipt( $data['receipt']);
        $borrowerPayment->setDate( $data['date']);
        $borrowerPayment->setAmount($data['amount']);
        $borrowerPayment->setStatus($data['status']);
        $borrowerPayment->setPhone($data['phone']);
        $borrowerPayment->setDetails($data['details']);
        $borrowerPayment->save();
    }
} 