<?php

namespace Zidisha\Repayment;

use Illuminate\Queue\Jobs\Job;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Html\BootstrapForm;

class RepaymentService
{

    private $paymentQuery;
    private $borrowerQuery;

    public function __construct(BorrowerPaymentQuery $paymentQuery, BorrowerQuery $borrowerQuery)
    {

        $this->paymentQuery = $paymentQuery;
        $this->borrowerQuery = $borrowerQuery;
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
}
