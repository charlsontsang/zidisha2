<?php
namespace Zidisha\Payment;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Currency\Money;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;
use Zidisha\Mail\LenderMailer;

class BalanceService
{
    private $transactionService;
    private $lenderMailer;

    public function __construct(TransactionService $transactionService, LenderMailer $lenderMailer)
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
    }

    public function uploadFunds(Payment $payment)
    {
        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            if ($payment->getTotalAmount()->isPositive()) {
                $this->transactionService->addUploadFundTransaction($con, $payment);
            }

            if ($payment->getDonationAmount()->isPositive()) {
                $this->transactionService->addDonation($con, $payment);
            }
        } catch (\Exception $e) {
            $con->rollback();
            throw $e;
        }

        $con->commit();
    }

    public function payWithdrawRequest(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->setPaid(true);
        $withdrawalRequest->save();

        $this->lenderMailer->sendPaypalWithdrawMail($withdrawalRequest->getLender(), $withdrawalRequest->getAmount());
    }
}
