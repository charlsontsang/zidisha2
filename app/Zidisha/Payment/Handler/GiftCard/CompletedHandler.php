<?php
namespace Zidisha\Payment\Handler\GiftCard;

use Zidisha\Lender\GiftCardService;
use Zidisha\Loan\LoanService;
use Zidisha\Mail\LenderMailer;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    private $balanceService;
    private $giftCardService;
    private $lenderMailer;

    public function __construct(
        BalanceService $balanceService,
        GiftCardService $giftCardService,
        LenderMailer $lenderMailer
    ) {
        $this->balanceService = $balanceService;
        $this->giftCardService = $giftCardService;
        $this->lenderMailer = $lenderMailer;
    }

    public function process()
    {
        $payment = $this->payment;

        $this->balanceService->uploadFunds($payment);
        $this->giftCardService->completeGiftCardTransaction($payment->getGiftCardTransaction());
        if ($this->payment->getAmount()->isPositive()) {
            $this->lenderMailer->sendFundUploadMail($this->payment->getLender(), $this->payment->getAmount());
        }

        if ($this->payment->getDonationAmount()->isPositive()) {
            $this->lenderMailer->sendDonationMail($this->payment->getLender(), $this->payment->getDonationAmount());
        }

        return $this;
    }

    public function redirect()
    {
        \Session::forget('giftCard');
        \Flash::success("Thanks for your purchase!");
        return \Redirect::route('lender:gift-cards:track');
    }
}
