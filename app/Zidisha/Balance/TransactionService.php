<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCard;
use Zidisha\Lender\Invite;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Loan;
use Zidisha\Payment\Payment;

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
    )
    {
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
    )
    {
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
    )
    {
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
    )
    {
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

        if (!$res2 || !$res3) {
            throw new \Exception();
        }

    }

    public function assertAmount(Money $amount)
    {
        if (!$amount->greaterThan(Money::create(0))) {
            throw new \Exception();
        }
    }

    public function addRedeemGiftCardTransaction(ConnectionInterface $con, GiftCard $giftCard)
    {
        $this->assertAmount($giftCard->getCardAmount());

        $giftTransaction = new Transaction();
        $giftTransaction
            ->setUserId($giftCard->getRecipientId())
            ->setAmount($giftCard->getCardAmount())
            ->setDescription('Gift Card Redemption')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::GIFT_REDEEM);

        $giftTransaction->save($con);
    }

    public function addUploadFundTransaction(ConnectionInterface $con, Payment $payment)
    {
        $this->assertAmount($payment->getTotalAmount());

        $transactionUpload = new Transaction();
        $transactionUpload->setUserId($payment->getLenderId());
        $transactionUpload->setAmount($payment->getTotalAmount());
        $transactionUpload->setDescription('Funds upload to lender account');
        $transactionUpload->setTransactionDate(new \DateTime());
        $transactionUpload->setType(Transaction::FUND_UPLOAD);
        $transactionUpload->setSubType(Transaction::FUND_UPLOAD);
        $transactionUpload->save($con);

        if ($payment->getTransactionFee()->greaterThan(Money::create(0))) {
            if ($payment->getPaymentMethod() == 'stripe') {
                $transactionType = Transaction::STRIPE_FEE;
                $description = 'Stripe transaction fee';
            } elseif ($payment->getPaymentMethod() == 'paypal') {
                $transactionType = Transaction::PAYPAL_FEE;
                $description = 'Paypal transaction fee';
            } else {
                throw \Exception('No matching payment method found.');
            }

            $transactionStripeFee = new Transaction();
            $transactionStripeFee->setUserId($payment->getLenderId());
            $transactionStripeFee->setAmount($payment->getTransactionFee()->multiply(-1));
            $transactionStripeFee->setDescription($description);
            $transactionStripeFee->setTransactionDate(new \DateTime());
            $transactionStripeFee->setType($transactionType);
            $transactionStripeFee->save($con);

            $transactionStripeAdmin = new Transaction();
            // TODO set use to admin
            $transactionStripeAdmin->setUserId(\Config::get('app.AdminId'));
            $transactionStripeAdmin->setAmount($payment->getTransactionFee());
            $transactionStripeAdmin->setDescription('Lender transaction fee');
            $transactionStripeAdmin->setTransactionDate(new \DateTime());
            $transactionStripeAdmin->setType($transactionType);
            $transactionStripeAdmin->save($con);
        }
    }

    public function addDonation(ConnectionInterface $con, Payment $payment)
    {
        $donationTransaction = new Transaction();
        $donationTransaction->setUserId($payment->getLenderId());
        $donationTransaction->setAmount($payment->getDonationAmount()->multiply(-1));
        $donationTransaction->setDescription('Donation to Zidisha');
        $donationTransaction->setTransactionDate(new \DateTime());
        $donationTransaction->setType(Transaction::DONATION);
        $donationTransaction->save($con);

        $donationTransaction = new Transaction();
        $donationTransaction->setUserId(\Config::get('app.AdminId'));
        $donationTransaction->setAmount($payment->getDonationAmount());
        $donationTransaction->setDescription('Donation from lender');
        $donationTransaction->setTransactionDate(new \DateTime());
        $donationTransaction->setType(Transaction::DONATION);
        $donationTransaction->save($con);
    }
}
