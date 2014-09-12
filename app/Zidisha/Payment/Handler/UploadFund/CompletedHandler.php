<?php
namespace Zidisha\Payment\Handler\UploadFund;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Mail\LenderMailer;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    private $balanceService;
    private $lenderMailer;

    public function __construct(BalanceService $balanceService, LenderMailer $lenderMailer)
    {
        $this->balanceService = $balanceService;
        $this->lenderMailer = $lenderMailer;
    }

    public function process()
    {
        $this->balanceService->uploadFunds($this->payment);
        $this->lenderMailer->sendFundUploadMail($this->payment->getLender(), $this->payment->getAmount());

        if ($this->payment->getDonationAmount()->isPositive()) {
            $this->lenderMailer->sendDonationMail($this->payment->getLender(), $this->payment->getDonationAmount());
        }
        return $this;
    }

    public function redirect()
    {
        \Flash::success("Successfully uploaded USD " . $this->payment->getCreditAmount()->getAmount());
        return \Redirect::route('lender:history');
    }
}