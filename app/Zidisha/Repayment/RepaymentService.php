<?php

namespace Zidisha\Repayment;

use Illuminate\Queue\Jobs\Job;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Currency\CurrencyService;
use Zidisha\Currency\Money;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Calculator\RepaymentCalculator;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Loan\Loan;

class RepaymentService
{

    private $paymentQuery;
    private $borrowerQuery;
    /**
     * @var \Zidisha\Currency\CurrencyService
     */
    private $currencyService;
    /**
     * @var \Zidisha\Balance\TransactionService
     */
    private $transactionService;

    public function __construct(BorrowerPaymentQuery $paymentQuery, BorrowerQuery $borrowerQuery, CurrencyService $currencyService, TransactionService $transactionService)
    {

        $this->paymentQuery = $paymentQuery;
        $this->borrowerQuery = $borrowerQuery;
        $this->currencyService = $currencyService;
        $this->transactionService = $transactionService;
    }

    public function addBorrowerPayment(Borrower $borrower, $data)
    {
        $borrowerPayment = new BorrowerPayment();
        $borrowerPayment->setBorrower($borrower);
        $borrowerPayment->setCountryCode($data['country_code']);
        $borrowerPayment->setReceipt($data['receipt']);
        $borrowerPayment->setDate($data['date']);
        $borrowerPayment->setAmount($data['amount']);
        $borrowerPayment->setStatus($data['status']);
        $borrowerPayment->setPhone($data['phone']);
        $borrowerPayment->setDetails($data['details']);
        $borrowerPayment->save();

        \Queue::push(
            'Zidisha\Repayment\RepaymentService@processBorrowerImportJob',
            array('id' => $borrowerPayment->getId())
        );
    }

    public function processBorrowerImportJob(Job $job, $data)
    {
        $id = $data['id'];
        $borrowerPayment = $this->paymentQuery->create()->findOneById($id);

        if ($borrowerPayment) {
            $this->processBorrowerPayment($borrowerPayment);
        }

        $job->delete();
    }

    public function processBorrowerPayment(BorrowerPayment $borrowerPayment)
    {
        if (!$borrowerPayment->getBorrower()) {
            $borrowerPayment
                ->setStatus(Borrower::PAYMENT_FAILED)
                ->setError('No borrower account associated with this phone number')
                ->save();
            return false;
        }
        if (!$borrowerPayment->getBorrower()->getActive()) {
            $borrowerPayment
                ->setStatus(Borrower::PAYMENT_FAILED)
                ->setError('Account is inactive')
                ->save();
            return false;
        }

        $loan_id = $borrowerPayment->getBorrower()->getActiveLoanId();
        if ($loan_id) {
            // TODO
            $result = 1; //$session->addRepayment($borrower_id, $loan_id, $date, $payment['amount'], $payment['id']);
            $form = []; //to do
            if ($result == 0 || $result == -1) {
                $error = array();
//                foreach ($form->getErrorArray() as $k => $v) {
//                    $error[] = "$k: $v";
//                }
                $borrowerPayment
                    ->setStatus(Borrower::PAYMENT_FAILED)
                    ->setError(implode('<br/>', $error))
                    ->save();

                return false;
            } else {
                $borrowerPayment
                    ->setStatus(Borrower::PAYMENT_PROCESSED)
                    ->save();
            }
        } else {
            $borrowerPayment
                ->setStatus(Borrower::PAYMENT_FAILED)
                ->setError('No active loan')
                ->save();
        }
        return true;
    }

    public function addBorrowerRefund($con, Loan $loan, Money $amount)
    {
        $borrowerRefund = new BorrowerRefund();
        $borrowerRefund
            ->setLoan($loan)
            ->setBorrower($loan->getBorrower())
            ->setAmount($amount);
        $borrowerRefund->save();
        
        return $borrowerRefund;
    }

    /**
     * @param Loan $loan
     * @param Money $amount
     * @return RepaymentCalculator
     */
    public function getRepaymentCalculator(Loan $loan, Money $amount)
    {
        $paidServiceFee = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterServiceFee()
            ->getNativeTotalAmount($loan->getCurrency());

        $forgivenAmount = ForgivenLoanQuery::create()
            ->filterByLoan($loan)
            ->getNativeTotalAmount($loan);

        $amounts = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->getNativeAmounts($loan->getCurrency());

        $calculator = new RepaymentCalculator($loan);
        $calculator
            ->setPaidAmount($amounts['paidAmount'])
            ->setTotalAmount($amounts['totalAmount'])
            ->setRepaymentAmount($amount)
            ->setPaidServiceFee($paidServiceFee)
            ->setForgivenAmount($forgivenAmount);

        return $calculator;
    }
    
    public function addRepayment(Loan $loan, \Datetime $date, Money $amount, BorrowerPayment $borrowerPayment = null)
    {        
        // Divide the payment in the lenders and the web site fee
        // 1. Get the web site fee %
        // 2. Get who all lended and how much
        // 3. substract he website fee out of this installment
        // 4. remaining money should be divided in lenders according to their proportion and added
        // 5. If the loan gets completed with this payment set the loan status to complete
        
        $calculator = $this->getRepaymentCalculator($loan, $amount);

        if ($calculator->unpaidAmount()->isNegative()) {
            throw new \Exception('Unpaid amount is negative');
        }
        
        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        $refundThreshold = $this->currencyService->convertFromUSD(Money::create(1), $loan->getCurrency(), $date);
        $refundAmount = $calculator->refundAmount($refundThreshold);

        if ($refundAmount->isPositive()) {
            $this->addBorrowerRefund($con, $loan, $refundAmount);
            $amount = $amount->subtract($refundAmount);
            $calculator->setRepaymentAmount($amount);
        }

        $this->transactionService->addInstallmentTransaction($con, $amount, $loan, $date);

        $nativeFeeAmount = $calculator->installmentServiceFee();
        $feeAmount = $this->currencyService->convertToUSD($nativeFeeAmount, $date);
        $this->transactionService->addInstallmentFeeTransaction($con, $loan, $feeAmount, $date);

        $bids = BidQuery::create()
            ->filterByLoan($loan)
            ->filterByActive(true)
            ->find();
        $loanRepayments = $calculator->loanRepayments($bids);

        /** @var $loanRepayment LoanRepayment */
        foreach ($loanRepayments as $loanRepayment) {
            $lender = $loanRepayment->getLender();
            $nativeLenderAmount = $loanRepayment->getAmount();
            $nativeLenderInviteCredit = $loanRepayment->getLenderInviteCredit();
            
            if ($nativeLenderAmount->isPositive()) {
                $lenderAmount = $this->currencyService->convertToUSD($nativeLenderAmount, $date);
                $this->transactionService->addRepaymentTransaction($con, $lenderAmount, $loan, $lender, $date);
            }
            
            if ($nativeLenderInviteCredit->isPositive()) {
                $lenderInviteCredit = $this->currencyService->convertToUSD($nativeLenderInviteCredit, $date);
                $this->transactionService->addLenderInviteCreditRepaymentTransaction($con, $lenderInviteCredit, $loan, $date);
            }
        }
        
        $updatedInstallments = $this->updateInstallmentSchedule($con, $loan, $amount, $date);
        
        // TODO
        // $database->setOntimeRepayCredit($rest4, $borrowerid, $amount);

        // TODO
        // $database->loanpaidback($borrowerid,$loanid);
        
        // TODO emails/sms
    }

    protected function updateInstallmentSchedule($con, Loan $loan, Money $amount, \Datetime $date)
    {
        $installments = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->orderById()// TODO order due date?
            ->find();
        
        $updatedInstallments = [];
        $installmentId = null;
        $paidAmount = $amount;
        
        foreach ($installments as $installment) {
            if ($paidAmount->isZero()) {
                break;
            }
            
            if ($installment->getNativeAmount()->isZero()) {
                continue;
            }
            
            if ($installment->isRepaid()) {
               continue;
            }
            
            $unpaidAmount = $installment->getUnpaidAmount();
            $installmentAmount = $unpaidAmount->min($paidAmount);
            $installment
                ->payAmount($installmentAmount)
                ->setPaidDate($date);
            $installment->save($con);
            $updatedInstallments[] = $installment;
            
            $paidAmount = $paidAmount->subtract($installmentAmount);
        }
        
        if ($updatedInstallments) {
            $installment = end($updatedInstallments);
            // TODO make installmentPayment
            reset($updatedInstallments);
        }
        
        return $updatedInstallments;
    }
}
