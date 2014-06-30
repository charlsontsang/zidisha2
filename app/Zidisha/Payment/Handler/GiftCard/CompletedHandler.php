<?php
namespace Zidisha\Payment\Handler\GiftCard;

use Zidisha\Lender\GiftCardService;
use Zidisha\Loan\LoanService;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    private $balanceService;
    private $giftCardService;

    public function __construct(BalanceService $balanceService, LoanService $loanService, GiftCardService $giftCardService)
    {
        $this->balanceService = $balanceService;
        $this->giftCardService = $giftCardService;
    }

    public function process()
    {
        $payment = $this->payment;

        $data = \Session::get('giftCard');

        $this->balanceService->uploadFunds($payment);
        $this->giftCardService->addGiftCard($payment->getLender(), $data);

        return $this;
    }

    public function redirect()
    {
        \Session::forget('giftCard');
        \Flash::success("GiftCard Successfully Made.");
        return \Redirect::route('lender:gift-cards:track');
    }
}
