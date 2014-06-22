<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Loan;

class TransactionService
{

    public function addDisbursementTransaction(ConnectionInterface $con, Money $amount, Loan $loan)
    {
        $this->assertAmount($amount);

        $disburseTransaction = new Transaction();
        $disburseTransaction
            ->setUser($loan->getBorrower()->getUser())
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
        $this->assertAmount($amount);

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

    public function addOutBidTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUser($loan->getBorrower()->getUser())
            ->setAmount($amount->getAmount())
            ->setDescription('Loan outbid')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::LOAN_OUTBID)
            ->setSubType(null);

        $bidTransactionSuccess = $bidTransaction->save($con);
        if (!$bidTransactionSuccess) {
            // Todo: Notify admin.
            throw new \Exception();
        }
    }

    public function addLoanBidCanceledTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan,
        Lender $lender
    ) {
        $this->assertAmount($amount);

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

    public function addUpdateBidTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUser($loan->getBorrower()->getUser())
            ->setAmount($amount->getAmount()->multiply(-1))
            ->setDescription('Loan bid')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::LOAN_BID)
            ->setSubType(Transaction::UPDATE_BID);

        $bidTransactionSuccess = $bidTransaction->save($con);
        if (!$bidTransactionSuccess) {
            // Todo: Notify admin.
            throw new \Exception();
        }
    }

    public function addPlaceBidTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUser($loan->getBorrower()->getUser())
            ->setAmount($amount->getAmount()->multiply(-1))
            ->setDescription('Loan bid')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::LOAN_BID)
            ->setSubType(Transaction::PLACE_BID);

        $bidTransactionSuccess = $bidTransaction->save($con);
        if (!$bidTransactionSuccess) {
            // Todo: Notify admin.
            throw new \Exception();
        }
    }

    public function assertAmount(
        Money $amount
    ) {
        if (!$amount->greaterThan(Money::create(0))) {
            throw new \Exception();
        }
    }
}
