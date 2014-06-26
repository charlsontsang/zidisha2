<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Currency\Money;
use Zidisha\Lender\Invite;
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
            ->setTransactionDate(new \DateTime())
            ->setDescription('Loan bid expired');
        $TransactionSuccess = $transaction->save($con);

        if (!$TransactionSuccess) {
            throw new \Exception();
        }
    }

    public function addOutBidTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan,
        Lender $lender
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUser($lender->getUser())
            ->setAmount($amount)
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
        Loan $loan,
        Lender $lender
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUser($lender->getUser())
            ->setAmount($amount->multiply(-1))
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
        Loan $loan,
        Lender $lender
    ) {
        $this->assertAmount($amount);

        $bidTransaction = new Transaction();
        $bidTransaction
            ->setUserId($lender->getId())
            ->setAmount($amount->multiply(-1))
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

    public function addFeeTransaction(ConnectionInterface $con, Money $amount, Loan $loan)
    {
        $this->assertAmount($amount);

        $feeTransactionBorrower = new Transaction();
        $feeTransactionBorrower
            ->setUser($loan->getBorrower()->getUser())
            ->setAmount($amount->multiply(-2.5))
            ->setDescription('Registration Fee')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::REGISTRATION_FEE);
        $TransactionSuccessBorrower = $feeTransactionBorrower->save($con);

        $feeTransactionAdmin = new Transaction();
        $feeTransactionAdmin
            ->setUserId(\Config::get('adminId'))
            ->setAmount($amount->multiply(2.5))
            ->setDescription('Registration Fee')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::REGISTRATION_FEE);
        $TransactionSuccessAdmin = $feeTransactionAdmin->save($con);

        if (!$TransactionSuccessBorrower || !$TransactionSuccessAdmin) {
            throw new \Exception();
        }
    }

    public function addLenderInviteTransaction(ConnectionInterface $con, Invite $invite)
    {
        $amount = 25;


        $transactionLender = new InviteTransaction();
        $transactionLender->setLender($invite->getLender());
        $transactionLender->setAmount($amount);
        $transactionLender->setDescription('Lender invite credit');
        $transactionLender->setTransactionDate(new \DateTime());
        $transactionLender->setType(Transaction::LENDER_INVITE_INVITER);
        $res2 = $transactionLender->save($con);

        $transactionInvitee = new InviteTransaction();
        $transactionInvitee->setLender($invite->getInvitee());
        $transactionInvitee->setAmount($amount);
        $transactionInvitee->setDescription('Lender invite credit');
        $transactionInvitee->setTransactionDate(new \DateTime());
        $transactionInvitee->setType(Transaction::LENDER_INVITE_INVITEE);
        $res3 = $transactionInvitee->save($con);

        if(!$res2 || !$res3) {
            throw new \Exception();
        }

    }

    public function assertAmount(Money $amount)
    {
        if (!$amount->greaterThan(Money::create(0))) {
            throw new \Exception();
        }
    }


}
