<?php
namespace Zidisha\Payment\Handler\UploadFund;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    /**
     * @var \Zidisha\Payment\BalanceService
     */
    private $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function process()
    {
        $this->balanceService->uploadFunds($this->payment);

        return $this;
    }

    public function redirect()
    {
        \Flash::success("Successfully uploaded USD " . $this->payment->getCreditAmount()->getAmount());
        return \Redirect::route('lender:history');
    }
}