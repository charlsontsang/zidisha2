<?php
namespace Zidisha\Payment;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Currency\Money;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;

class BalanceService
{

    /**
     * @var \Zidisha\Balance\TransactionService
     */
    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function uploadFunds(Payment $payment)
    {
        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $this->transactionService->addUploadFundTransaction($con, $payment);

            if ($payment->getDonationAmount()->greaterThan(Money::create(0))) {
                $this->transactionService->addDonation($con, $payment);
            }
        } catch (\Exception $e) {
            $con->rollback();
            throw $e;
        }

        $con->commit();

    }
} 