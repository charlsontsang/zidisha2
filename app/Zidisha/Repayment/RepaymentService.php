<?php

namespace Zidisha\Repayment;

use Illuminate\Queue\Jobs\Job;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Currency\Converter;
use Zidisha\Currency\CurrencyService;
use Zidisha\Currency\Money;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Calculator\RepaymentCalculator;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Loan\Loan;
use Zidisha\Vendor\PropelDB;

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
        $borrowerRefund->save($con);
        
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
            ->getForeignTotalAmount($loan->getCurrency());

        $forgivenAmount = ForgivenLoanQuery::create()
            ->filterByLoan($loan)
            ->getForeignTotalAmount($loan->getCurrency());

        $amounts = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->getForeignAmounts($loan->getCurrency());

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
        $calculator = $this->getRepaymentCalculator($loan, $amount);

        if ($calculator->unpaidAmount()->isNegative()) {
            throw new \Exception('Unpaid amount is negative');
        }

        list($loanRepayments) = PropelDB::transaction(function($con) use($calculator, $loan, $date, $amount) {
            $borrower = $loan->getBorrower();

            $exchangeRate = $this->currencyService->getExchangeRate($loan->getCurrency(), $date);
            
            $refundThreshold = Converter::fromUSD(Money::create(1), $loan->getCurrency(), $exchangeRate);
            $refundAmount = $calculator->refundAmount($refundThreshold);

            if ($refundAmount->isPositive()) {
                $this->addBorrowerRefund($con, $loan, $refundAmount);
                $amount = $amount->subtract($refundAmount);
                $calculator->setRepaymentAmount($amount);
            }

            $feeAmount = $calculator->repaymentServiceFee();
            $feeAmountUsd = Converter::toUSD($feeAmount, $exchangeRate);
            $this->transactionService->addInstallmentFeeTransaction($con, $exchangeRate, $feeAmountUsd, $loan, $date);

            $bids = BidQuery::create()
            ->filterBidsToRepay($loan)
            ->find();
            $loanRepayments = $calculator->loanRepayments($exchangeRate, $bids);

                /** @var $loanRepayment LoanRepayment */
            foreach ($loanRepayments as $loanRepayment) {
                $lender = $loanRepayment->getLender();
                $lenderAmount = $loanRepayment->getAmount();
                $lenderInviteCredit = $loanRepayment->getLenderInviteCredit();

                if ($lenderAmount->isPositive()) {
                    $this->transactionService->addRepaymentTransaction($con, $lenderAmount, $loan, $lender, $date);
                }

                if ($lenderInviteCredit->isPositive()) {
                    $this->transactionService->addLenderInviteCreditRepaymentTransaction($con, $lenderInviteCredit, $loan, $date);
                }
            }

            $amountUsd = Converter::toUSD($amount, $exchangeRate);
            $this->transactionService->addInstallmentTransaction($con, $exchangeRate, $amountUsd, $loan, $date);

            $this->updateInstallmentSchedule($con, $loan, $amount, $date);
            
            if ($calculator->isRepaid()) {
                $loan->setStatus(Loan::REPAID);
                $loan->save($con);

                $borrower
                    ->setActiveLoan(null)
                    ->setLoanStatus(Loan::REPAID);
                $borrower->save($con);
            }

            // TODO
            // $database->setOntimeRepayCredit($rest4, $borrowerid, $amount);
            
            return [$loanRepayments];
        });


        if ($calculator->isRepaid()) {
            /** @var LoanRepayment $loanRepayment */
            foreach ($loanRepayments as $loanRepayment) {
                $lender = $loanRepayment->getLender();
                // Send email, session.php 838
            }
        }
        
        // TODO emails/sms/sift science
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
            
            if ($installment->getAmount()->isZero() || $installment->isRepaid()) {
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
            /** @var Installment $installment */
            $installment = $updatedInstallments[count($updatedInstallments) - 1];
            
            $installmentPayment = new InstallmentPayment();
            $installmentPayment
                ->setInstallmentId($installment->getId())
                ->setBorrowerId($installment->getBorrowerId())
                ->setLoanId($loan->getId())
                ->setPaidAmount($amount)
                ->setPaidDate($date);
            
            $installmentPayment->save($con);
        }
        
        return $updatedInstallments;
    }

    public function getRepaymentSchedule(Loan $loan)
    {
        $repaymentSchedule = [

        ];
        $i = 0;
        $installments = InstallmentQuery::create()
            ->filterByLoan($loan)
            ->orderByDueDate('asc')
            ->find();
        $installmentsPayments = InstallmentPaymentQuery::create()
            ->filterByLoan($loan)
            ->orderByPaidDate('asc')
            ->find();
        $payments = $installmentsPayments->getData();

        $zero = Money::create(0, $loan->getCurrency());
        $currentPayment = next($payments);
        $currentAmount = $currentPayment ? $currentPayment->getPaidAmount() : $zero;

        foreach ($installments as $installment) {
            if (!$installment->getAmount()->isPositive()) {
                continue;
            }
            $repaymentSchedule[$i]['installment'] = $installment;
            $repaymentSchedule[$i]['payments'] = [];

            $openAmount = $installment->getAmount();

            while ($currentPayment && $openAmount->isPositive()) {
                $payment = $currentPayment;
                if ($openAmount->lessThan($currentAmount)) {
                    $amount = $openAmount;
                    $currentAmount = $currentAmount->subtract($openAmount);
                    $openAmount = $zero;
                } else {
                    $amount = $currentAmount;
                    $currentPayment = next($payments);
                    $currentAmount = $currentPayment ? $currentPayment->getPaidAmount() : $zero;
                    $openAmount = $openAmount->subtract($amount);
                }

                $repaymentSchedule[$i]['payments'][] = [
                    'payment' => $payment,
                    'amount'  => $amount
                ];
            }
            $i++;
        }
        return $repaymentSchedule;
    }
}
