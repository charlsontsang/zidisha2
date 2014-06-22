<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

class TransactionService
{

    protected $con;

    public function addDisbursementTransaction(ConnectionInterface $con, Loan $loan, Money $amount)
    {
        if ($amount->getAmount() <= 0) {
            throw new \Exception();
        }
        $disburseTransaction = new Transaction();
        $disburseTransaction
            ->setUser($loan->getBorrower())
            ->setAmount($amount->multiply(-1))
            ->setDescription('Got amount from loan')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::DISBURSEMENT);
        $TransactionSuccess = $disburseTransaction->save($con);

        if (!$TransactionSuccess) {
            throw new \Exception();
        }
    }
}
