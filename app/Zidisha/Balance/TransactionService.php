<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
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

    public function addLoanBidExpiredTransaction(ConnectionInterface $con, Money $amount, Loan $loan, Lender $lender)
    {
        if (!$amount->greaterThan(Money::create(0))) {
            throw new \Exception();
        }

        $transaction = new Transaction();
        $transaction
            ->setAmount($amount)
            ->setUserId($lender->getId())
            ->setLoan($loan)
            ->setType(Transaction::LOAN_OUTBID)
            ->setSubType(Transaction::LOAN_BID_EXPIRED)
            ->setDescription('Loan bid expired');
        $TransactionSuccess = $transaction->save($con);

        if (!$TransactionSuccess) {
            throw new \Exception();
        }
    }

    public function addLoanBidCanceledTransaction(ConnectionInterface $con, Money $amount, Loan $loan, Lender $lender)
    {
        if (!$amount->greaterThan(Money::create(0))) {
            throw new \Exception();
        }

        $transaction = new Transaction();
        $transaction
            ->setAmount($amount)
            ->setUserId($lender->getId())
            ->setLoan($loan)
            ->setType(Transaction::LOAN_OUTBID)
            ->setSubType(Transaction::LOAN_BID_CANCELED)
            ->setDescription('Loan bid cancelled');
        $TransactionSuccess = $transaction->save($con);

        if (!$TransactionSuccess) {
            throw new \Exception();
        }
    }
}
