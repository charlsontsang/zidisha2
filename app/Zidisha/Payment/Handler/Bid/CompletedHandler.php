<?php
namespace Zidisha\Payment\Handler\Bid;

use Zidisha\Loan\LoanService;
use Zidisha\Mail\LenderMailer;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\PaymentHandler;

class CompletedHandler extends PaymentHandler
{

    private $loanService;
    private $balanceService;
    private $lenderMailer;

    public function __construct(BalanceService $balanceService, LoanService $loanService, LenderMailer $lenderMailer)
    {
        $this->loanService = $loanService;
        $this->balanceService = $balanceService;
        $this->lenderMailer = $lenderMailer;
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
        return \Redirect::route('loan:success', ['id' => $this->payment->getLoanId()]);
    }
}
