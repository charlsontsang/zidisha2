<?php

namespace Zidisha\Lender;

use Propel\Runtime\Propel;
use Whoops\Example\Exception;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionService;
use Zidisha\Currency\Money;
use Faker\Factory as Faker;
use Zidisha\Mail\LenderMailer;
use Zidisha\Payment\GiftCardPayment;
use Zidisha\Payment\Payment;

class GiftCardService
{

    private $transactionService;
    private $lenderMailer;

    public function __construct(TransactionService $transactionService, LenderMailer $lenderMailer)
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
    }

    public function validateCode($redemptionCode)
    {
        $count = GiftCardQuery::create()
            ->filterByCardCode($redemptionCode)
            ->count();
        if ($count > 1) {
            return 'comments.flash.duplicate-code';
        }
        $giftCard = GiftCardQuery::create()
            ->filterByCardCode($redemptionCode)
            ->findOne();
        if (!$giftCard) {
            return 'comments.flash.invalid-code';
        }

        if ($giftCard->getStatus() == 1) {
            $giftCard = GiftCardQuery::create()
                ->filterByCardCode($redemptionCode)
                ->findOne();
            if ($giftCard->getClaimed() == 1) {
                return 'comments.flash.redeemed-code';
            } else {
                $currentDate = new \DateTime();
                if ($giftCard->getExpireDate() < $currentDate) {
                    return 'comments.flash.expired-code';
                }
            }
        } elseif ($giftCard->getStatus() == 0) {
            return 'comments.flash.invalid-code';
        }
    }

    public function addGiftCard(Lender $lender, $data)
    {
        $data += [
            'recipientEmail'    => null,
            'recipientName'     => null,
            'fromName'          => null,
            'message'           => null,
            'confirmationEmail' => null,
        ];

        $amount = Money::create($data['amount'], 'USD');
        $faker = Faker::create();

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        $giftCard = new GiftCard();
        $giftCard
            ->setLender($lender)
            ->setTemplate($data['template'])
            ->setOrderType($data['orderType'])
            ->setCardAmount($amount)
            ->setRecipientEmail($data['recipientEmail'])
            ->setRecipientName($data['recipientName'])
            ->setFromName($data['fromName'])
            ->setMessage($data['message'])
            ->setDate(new \DateTime())
            ->setExpireDate(strtotime('+1 year'))
            ->setCardCode($faker->creditCardNumber)
            ->setConfirmationEmail($data['confirmationEmail']);

        try {
            $giftCard->save($con);

            $this->transactionService->purchaseGiftCardTransaction($con, $giftCard);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        $this->lenderMailer->sendGiftCardMailToSender($giftCard);
        if ($data['recipientEmail']) {
            $this->lenderMailer->sendGiftCardMailToRecipient($giftCard);
        }

        return $giftCard;
    }

    public function redeemGiftCard(Lender $recipient, $redemptionCode)
    {
        $giftCard = GiftCardQuery::create()
            ->findOneByCardCode($redemptionCode);

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $giftCard
                ->setClaimed(1)
                ->setRecipient($recipient);

            $giftCard->save($con);

            $this->transactionService->addRedeemGiftCardTransaction($con, $giftCard);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $giftCard;
    }

    public function UpdateGiftCardStatus(Payment $payment)
    {
        $giftCardTransaction = $payment->getGiftCardTransaction();
        $giftCardTransaction->setStatus(1);
        $giftCardTransaction->save();

        $giftCards = GiftCardQuery::create()
            ->filterByGiftCardTransaction($giftCardTransaction)
            ->find();

        foreach ($giftCards as $giftCard) {
            $giftCard->setStatus(1);
            $giftCard->save();
        }
    }

}
