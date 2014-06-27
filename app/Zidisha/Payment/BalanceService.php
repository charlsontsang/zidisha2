<?php
namespace Zidisha\Payment;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionService;
use Zidisha\Currency\Money;

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
//        $database->setTransaction(ADMIN_ID,$donation_amount,'Donation from lender',0,0,DONATION);
//        $database->setTransaction($userid,$donationamt,'Donation to Zidisha',0,0,DONATION);
        } catch (\Exception $e) {
            $con->rollback();
            throw $e;
        }

        $con->commit();

    }
} 