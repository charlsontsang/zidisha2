<?php
namespace Zidisha\Payment\Handler\Bid;

use Zidisha\Loan\LoanService;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    private $loanService;
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
            'amount' => $payment->getAmount()->getAmount(),
            'isLenderInviteCredit' => $payment->getIsLenderInviteCredit(),
        ];

        $this->balanceService->uploadFunds($payment);
        $this->loanService->placeBid($payment->getLoan(), $payment->getLender(), $data);

        return $this;
    }

    public function redirect()
    {
        return \Redirect::route('loan:success', ['id' => $this->payment->getLoanId()]);
    }
}
