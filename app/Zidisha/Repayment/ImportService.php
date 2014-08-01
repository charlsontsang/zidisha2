<?php

namespace Zidisha\Repayment;


use PHPExcel_IOFactory;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;

class ImportService
{

    /**
     * @var RepaymentService
     */
    private $repaymentService;

    public function __construct(RepaymentService $repaymentService)
    {

        $this->repaymentService = $repaymentService;
    }

    public function importBorrowerPayments($country, $file)
    {
        $countries = array(
            'KE' => 'Kenya',
            'GH' => 'Ghana',
        );

        if (!isset($countries[$country])) {
            return false;
        }
        $method = 'importBorrowerPayments' . $countries[$country];

        $payments = $this->$method($file);

//        if ($payments === false) {
//            return false;
//        }

        $complete = $incomplete = $skipped = 0;
        foreach ($payments as $payment) {
            $result = $this->addBorrowerPayment($payment);
            switch ($result) {
                case Borrower::PAYMENT_COMPLETE:
                    $complete += 1;
                    break;
                case Borrower::PAYMENT_INCOMPLETE:
                    $incomplete += 1;
                    break;
                default:
                    $skipped += 1;
            }
//            if ($result == false) {
//                return false;
//            }
        }

        return compact('complete', 'incomplete', 'skipped');
    }

    protected function importBorrowerPaymentsCountry($file, $firstColumn, $headerColumns)
    {

        $reader = PHPExcel_IOFactory::load($file);
        $sheet = $reader->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        //$highestColumn = $sheet->getHighestColumn();
        // tmp fix, the line above can return non-alphabetic characters (e.g. IV)
        $highestColumn = 'Z';

        $headerRow = 0;
        do {
            $headerRow += 1;
            $cell = $sheet->getCell(("A$headerRow"))->getValue();
        } while ($cell != $firstColumn && $headerRow < $highestRow);

        $columns = array();
        foreach (range('A', $highestColumn) as $column) {
            $cell = $sheet->getCell($column . $headerRow)->getValue();
            if (isset($headerColumns[$cell])) {
                $columns[$headerColumns[$cell]] = $column;
            }
        }

        if (count($headerColumns) != count($columns)) {
            return false;
        }

        $payments = array();
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $payment = array();
            foreach ($columns as $k => $column) {
                $payment[$k] = trim($sheet->getCell($column . $row)->getValue());
            }
            $payments[] = $payment;
        }

        return $payments;
    }

    protected function importBorrowerPaymentsKenya($file)
    {
        $payments = $this->importBorrowerPaymentsCountry(
            $file,
            'Receipt',
            array(
                'Receipt' => 'receipt',
                'Date' => 'date',
                'Status' => 'status',
                'Paid In' => 'amount',
                'Other Party Info' => 'info',
                'Transaction Type' => 'transaction_type',
                'Details' => 'details',
            )
        );

        if ($payments === false) {
            return false;
        }

        $borrower_payments = array();
        foreach ($payments as $payment) {
            if ($payment['status'] != 'Completed' || $payment['transaction_type'] != 'Pay Utility') {
                continue;
            }

            $payment['country_code'] = 'KE';

            $date = new \DateTime($payment['date']);
            $payment['date'] = $date->format('Y-m-d G:i:s');

            $parts = explode('-', $payment['info']);
            $payment['phone'] = trim($parts[0]);

            $borrower_payments[] = $payment;
        }

        return $borrower_payments;
    }

    protected function importBorrowerPaymentsGhana($file)
    {
        $payments = $this->importBorrowerPaymentsCountry(
            $file,
            'Account Reference Number',
            array(
                'Date' => 'date',
                'Time' => 'time',
                'Transaction ID' => 'receipt',
                'Transaction Type' => 'transaction_type',
                'Description' => 'details',
                'Amount' => 'amount',
            )
        );

        if ($payments === false) {
            return false;
        }

        $borrower_payments = array();
        foreach ($payments as $payment) {
            foreach ($payment as $k => $v) {
                $payment[$k] = trim($v, "'");
            }
            if ($payment['transaction_type'] != 'Payment Received') {
                continue;
            }

            $payment['country_code'] = 'GH';

            $date = \DateTime::createFromFormat('d/m/Y G:i:s', $payment['date'] . ' ' . $payment['time']);
            $payment['date'] = $date->format('Y-m-d G:i:s');

            $matched = preg_match('/\(.*\)/', $payment['details'], $matches);
            $payment['phone'] = $matched ? trim($matches[0], ' ()') : '';

            $borrower_payments[] = $payment;
        }

        return $borrower_payments;
    }

    protected function addBorrowerPayment($payment)
    {
        $borrower = BorrowerQuery::create()
            ->useProfileQuery()
                ->filterByPhoneNumber($payment['phone'])
            ->endUse()
            ->filterByActive(true)
            ->findOne();

        if (!$borrower) {
            return false;
        }

        $payment['borrower_id'] = $borrower ? $borrower->getId() : null;

        $validator = $this->getBorrowerPaymentValidator($payment);
        $payment['status'] = $validator->fails() ? Borrower::PAYMENT_INCOMPLETE : Borrower::PAYMENT_COMPLETE;

        if ($this->alreadyImportedBorrowerPayment($payment['country_code'], $payment['receipt'])) {
            return false;
        }

        $this->repaymentService->addBorrowerPayment($borrower, $payment);

        return $payment['status'];
    }

    protected function alreadyImportedBorrowerPayment($country_code, $receipt)
    {
        $result = BorrowerPaymentQuery::create()
            ->filterByCountryCode($country_code)
            ->filterByReceipt($receipt)
            ->count();

        return $result > 0;
    }

    public function getBorrowerPaymentValidator($payment)
    {
        $rules = [
            'country_code' => 'required',
            'receipt' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'borrower_id' => 'required|min:1',
            'phone' => 'required',
        ];

        return \Validator::make($payment, $rules);
    }
}
