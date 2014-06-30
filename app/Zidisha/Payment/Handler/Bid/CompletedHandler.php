<?php
namespace Zidisha\Payment\Handler\Bid;

use Zidisha\Loan\LoanService;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{


    /**
     * @var \Zidisha\Loan\LoanService
     */
    private $loanService;
    /**
     * @var \Zidisha\Payment\BalanceService
     */
    private $balanceService;

    public function __construct(BalanceService $balanceService, LoanService $loanService)
    {
        $this->loanService = $loanService;
        $this->balanceService = $balanceService;
    }

    public function process()
    {
        $payment = $this->payment;

        $data = [
            'interestRate' => $payment->getInterestRate(),
            'amount' => $payment->getBidAmount()->getAmount()
        ];

        $this->balanceService->uploadFunds($payment);
        $this->loanService->placeBid($payment->getLoan(), $payment->getLender(), $data);

        return $this;
    }

    public function redirect()
    {
        //Todo: sucess message
        \Flash::success("Place bid successfully " . $this->payment->getBidAmount()->getAmount());
        return \Redirect::route('loan:index', ['id' => $this->payment->getLoanId()]);
    }
}
