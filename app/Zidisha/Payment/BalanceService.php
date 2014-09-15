<?php
namespace Zidisha\Payment;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Currency\Money;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;
use Zidisha\Lender\Lender;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Vendor\PropelDB;

class BalanceService
{
    private $transactionService;
    private $lenderMailer;
    private $adminMailer;

    public function __construct(TransactionService $transactionService, LenderMailer $lenderMailer,
                                AdminMailer $adminMailer)
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
        $this->adminMailer = $adminMailer;
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

    public function addWithdrawRequest(Lender $lender, $data)
    {
        $amount = Money::create($data['withdrawAmount']);
        $withdrawalRequest = new WithdrawalRequest();
        $withdrawalRequest->setLender($lender)
            ->setAmount($amount)
            ->setPaypalEmail($data['paypalEmail']);

        PropelDB::transaction(function($con) use ($lender, $amount, $withdrawalRequest) {
                $this->transactionService->addWithdrawFundTransaction($con, $amount, $lender);
                $withdrawalRequest->save($con);
            });
        $this->lenderMailer->sendWithdrawalRequestMail($lender, $withdrawalRequest);
        return $withdrawalRequest;
    }
}
