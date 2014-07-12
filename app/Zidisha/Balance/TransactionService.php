<?php

namespace Zidisha\Balance;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Admin\Setting;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCard;
use Zidisha\Lender\GiftCardTransaction;
use Zidisha\Lender\Invite;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Loan;
use Zidisha\Payment\Payment;

class TransactionService
{

    public function assertAmount(Money $amount)
    {
        if (!$amount->isPositive()) {
            throw new \Exception('Amount should be positive.');
        }
    }

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

        $disburseTransaction->save($con);
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

        $transaction->save($con);
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

        $bidTransaction->save($con);
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

        $transaction->save($con);
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

        $bidTransaction->save($con);
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

        $bidTransaction->save($con);
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

        $feeTransactionBorrower->save($con);

        $feeTransactionAdmin = new Transaction();
        $feeTransactionAdmin
            ->setUserId(Setting::get('site.adminId'))
            ->setAmount($amount->multiply(2.5))
            ->setDescription('Registration Fee')
            ->setLoan($loan)
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::REGISTRATION_FEE);

        $feeTransactionAdmin->save($con);
    }

    public function addLenderInviteTransaction(ConnectionInterface $con, Invite $invite)
    {
        $amount = Money::create(25);

        $transactionLender = new InviteTransaction();
        $transactionLender
            ->setLender($invite->getLender())
            ->setAmount($amount)
            ->setDescription('Lender invite credit')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::LENDER_INVITE_INVITER);
        $transactionLender->save($con);

        $transactionInvitee = new InviteTransaction();
        $transactionInvitee
            ->setLender($invite->getInvitee())
            ->setAmount($amount)
            ->setDescription('Lender invite credit')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::LENDER_INVITE_INVITEE);
        $transactionInvitee->save($con);
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
        $transactionUpload
            ->setUserId($payment->getLenderId())
            ->setAmount($payment->getTotalAmount())
            ->setDescription('Funds upload to lender account')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::FUND_UPLOAD)
            ->setSubType(Transaction::FUND_UPLOAD);
        $transactionUpload->save($con);


        if ($payment->getTransactionFee()->isPositive()) {
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
            $transactionStripeFee
                ->setUserId($payment->getLenderId())
                ->setAmount($payment->getTransactionFee()->multiply(-1))
                ->setDescription($description)
                ->setTransactionDate(new \DateTime())
                ->setType($transactionType);
            $transactionStripeFee->save($con);

            $transactionStripeAdmin = new Transaction();
            $transactionStripeAdmin
                ->setUserId(Setting::get('site.adminId'))
                ->setAmount($payment->getTransactionFee())
                ->setDescription('Lender transaction fee')
                ->setTransactionDate(new \DateTime())
                ->setType($transactionType);
            $transactionStripeAdmin->save($con);
        }
    }

    public function addDonation(ConnectionInterface $con, Payment $payment)
    {
        $donationTransaction = new Transaction();
        $donationTransaction
            ->setUserId($payment->getLenderId())
            ->setAmount($payment->getDonationAmount()->multiply(-1))
            ->setDescription('Donation to Zidisha')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::DONATION);
        $donationTransaction->save($con);


        $donationTransaction = new Transaction();
        $donationTransaction
            ->setUserId(Setting::get('site.adminId'))
            ->setAmount($payment->getDonationAmount())
            ->setDescription('Donation from lender')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::DONATION);
        $donationTransaction->save($con);
    }

    public function addInstallmentTransaction(ConnectionInterface $con, Money $amount, Loan $loan, \DateTime $date)
    {
        $this->assertAmount($amount);

        $transaction = new Transaction();
        $transaction
            ->setUserId($loan->getBorrowerId())
            ->setAmount($amount)
            ->setDescription('Loan installment')
            ->setLoan($loan)
            ->setTransactionDate($date)
            ->setType(Transaction::LOAN_BACK);

        $transaction->save($con);
    }

    public function addInstallmentFeeTransaction(ConnectionInterface $con, Money $amount, Loan $loan, \DateTime $date)
    {
        $this->assertAmount($amount);

        $transaction = new Transaction();
        $transaction
            ->setUserId(Setting::get('site.adminId'))
            ->setAmount($amount)
            ->setDescription('Fee')
            ->setLoan($loan)
            ->setTransactionDate($date)
            ->setType(Transaction::FEE);

        $transaction->save($con);
    }

    public function addRepaymentTransaction(ConnectionInterface $con, Money $amount, Loan $loan, Lender $lender, \DateTime $date)
    {
        $this->assertAmount($amount);

        $transaction = new Transaction();
        $transaction
            ->setUserId($lender->getId())
            ->setAmount($amount)
            ->setDescription('Loan repayment received')
            ->setLoan($loan)
            ->setTransactionDate($date)
            ->setType(Transaction::LOAN_BACK_LENDER);

        $transaction->save($con);
    }

    public function addLenderInviteCreditRepaymentTransaction(
        ConnectionInterface $con,
        Money $amount,
        Loan $loan,
        \DateTime $date
    ) {
        $this->assertAmount($amount);

        $transaction = new Transaction();
        $transaction
            ->setUserId(Setting::get('site.YCAccountId'))
            ->setAmount($amount)
            ->setDescription('Loan repayment received')
            ->setLoan($loan)
            ->setTransactionDate($date)
            ->setType(Transaction::LOAN_BACK_LENDER);

        $transaction->save($con);
    }

    public function purchaseGiftCardTransaction(ConnectionInterface $con, GiftCard $giftCard)
    {
        $this->assertAmount($giftCard->getCardAmount());

        $giftCardTransaction = new Transaction();
        $giftCardTransaction->setUserId($giftCard->getLender()->getUser()->getId())
            ->setAmount($giftCard->getCardAmount()->multiply(-1))
            ->setDescription("Gift Card Purchase")
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::GIFT_PURCHASE);
        $giftCardTransaction->save($con);

        $giftCardTransaction = new Transaction();
        $giftCardTransaction->setUserId(Setting::get('site.adminId'))
            ->setAmount($giftCard->getCardAmount())
            ->setDescription("Gift Card Purchase")
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::GIFT_PURCHASE);
        $giftCardTransaction->save($con);

        $giftPurchaseTransaction = new GiftCardTransaction();
        $giftPurchaseTransaction->setLender($giftCard->getLender())
            ->setAmount($giftCard->getCardAmount()->getAmount())
            ->setTransactionId($giftCardTransaction->getId())
            ->setDate(new \DateTime())
            ->setTotalCards(1)
            ->setTransactionType("Gift Card");
        $giftCard->setGiftCardTransaction($giftPurchaseTransaction);

        $giftCard->save($con);

    }

    public function addConvertToDonationTransaction(ConnectionInterface $con,Lender $lender,Money $amount)
    {
        $this->assertAmount($amount);

        $transaction = new Transaction();
        $transaction
            ->setUserId($lender->getId())
            ->setAmount($amount->multiply(-1))
            ->setDescription('Donation to Zidisha')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::DONATION)
            ->setSubType(Transaction::DONATE_BY_ADMIN);
        $transaction->save($con);

        $transactionDonation = new Transaction();
        $transactionDonation
            ->setUserId(Setting::get('site.adminId'))
            ->setAmount($amount)
            ->setDescription('Donation from lender')
            ->setTransactionDate(new \DateTime())
            ->setType(Transaction::DONATION)
            ->setSubType(Transaction::DONATE_BY_ADMIN);
        $transactionDonation->save($con);
    }
}
