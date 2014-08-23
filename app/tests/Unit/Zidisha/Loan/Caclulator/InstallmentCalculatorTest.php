<?php

namespace Unit\Zidisha\Loan;

use Carbon\Carbon;
use \DateTime;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Money;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;

class InstallmentCalculatorTest extends \TestCase
{

    public function testInterestCalculations()
    {
        $loans = [
            [
                'amount'             => 3000,
                'installmentAmount'  => 300,
                'period'             => 12,
                'installmentPeriod'  => Loan::MONTHLY_INSTALLMENT,
                'extraDays'          => 0,
                'lenderInterestRate' => 10,
                // expected
                // annualInterestRateRatio = 1,
                'lenderInterest'     => 300,
                'serviceFee'         => 150,
                'totalInterest'      => 450,
                'totalAmount'        => 3450,
            ],
            [
                'amount'             => '1000',
                'installmentAmount'  => '200',
                'period'             => '6',
                'installmentPeriod'  => Loan::WEEKLY_INSTALLMENT,
                'extraDays'          => '3',
                'lenderInterestRate' => '4',
                // expected
                'lenderInterest'     => '4.95',
                'serviceFee'         => '6.18',
                'totalInterest'      => '11.13',
                'totalAmount'        => '1011.13',
            ],
        ];

        foreach ($loans as $data) {
            $loan = $this->createLoan($data);
            
            $calculator = new InstallmentCalculator($loan);
            $this->assertEquals(
                $loan->getPeriod(),
                $calculator->period(Money::create($data['installmentAmount'], $loan->getCurrencyCode()))
            );
            $this->assertEquals(
                Money::create($data['lenderInterest'], $loan->getCurrencyCode()),
                $calculator->lenderInterest()->round(2)
            );
            $this->assertEquals(
                Money::create($data['serviceFee'], $loan->getCurrencyCode()),
                $calculator->serviceFee()->round(2)
            );
            $this->assertEquals(
                Money::create($data['totalInterest'], $loan->getCurrencyCode()),
                $calculator->totalInterest()->round(2)
            );
            $this->assertEquals(
                Money::create($data['totalAmount'], $loan->getCurrencyCode()),
                $calculator->totalAmount()->round(2)
            );
        }
    }

    protected function createLoan($data)
    {
        $data += [
            'status'                => Loan::OPEN,
            'maxLenderInterestRate' => 15,
            'serviceFeeRate'        => 5,
            'currencyCode'          => 'KES',
            'disbursedAt'            => new DateTime(),
        ];

        $loan = new Loan();
        $loan
            ->setAmount(Money::create($data['amount']))
            ->setPeriod($data['period'])
            ->setInstallmentPeriod($data['installmentPeriod'])
            ->setExtraDays($data['extraDays'])
            ->setMaxInterestRate($data['maxLenderInterestRate'] + $data['serviceFeeRate'])
            ->setLenderInterestRate($data['lenderInterestRate'])
            ->setServiceFeeRate($data['serviceFeeRate'])
            ->setStatus($data['status'])
            ->setCurrencyCode($data['currencyCode'])
            ->setBorrower(new Borrower())
            ->setDisbursedAt($data['disbursedAt']);
        
        return $loan;
    }

    public function testNthInstallmentDate()
    {
        $loan = new Loan();
        $calculator = new InstallmentCalculator($loan);

        $loan->setDisbursedAt(Carbon::createFromDate(2014, 12, 5));
        $this->assertEquals(Carbon::createFromDate(2015, 1, 5), $calculator->nthInstallmentDate(1));
        $this->assertEquals(Carbon::createFromDate(2015, 5, 5), $calculator->nthInstallmentDate(5));

        $loan->setDisbursedAt(Carbon::createFromDate(2015, 1, 31));
        $this->assertEquals(Carbon::createFromDate(2015, 2, 28), $calculator->nthInstallmentDate(1));
        $this->assertEquals(Carbon::createFromDate(2015, 3, 31), $calculator->nthInstallmentDate(2));
        $this->assertEquals(Carbon::createFromDate(2015, 4, 30), $calculator->nthInstallmentDate(3));

        $loan->setDisbursedAt(Carbon::createFromDate(2015, 12, 31));
        $this->assertEquals(Carbon::createFromDate(2016, 2, 29), $calculator->nthInstallmentDate(2));

        $loan->setDisbursedAt(Carbon::createFromDate(2016, 2, 29));
        $this->assertEquals(Carbon::createFromDate(2017, 2, 28), $calculator->nthInstallmentDate(12));
    }
    
    public function testGenerateLoanInstallments()
    {
        $loans = [
            [
                'amount'             => 3000,
                'installmentAmount'  => 300,
                'period'             => 12,
                'installmentPeriod'  => Loan::MONTHLY_INSTALLMENT,
                'extraDays'          => 0,
                'lenderInterestRate' => 10,
                // expected
                'iAmount'            => '287',
                'lastIAmount'        => '293',
            ],
            [
                'amount'             => '1000',
                'installmentAmount'  => '200',
                'period'             => '6',
                'installmentPeriod'  => Loan::WEEKLY_INSTALLMENT,
                'extraDays'          => '3',
                'lenderInterestRate' => '4',
                // expected
                'iAmount'            => '168',
                'lastIAmount'        => '171.13',
            ],
        ];

        foreach ($loans as $data) {
            $loan = $this->createLoan($data);

            $calculator = new InstallmentCalculator($loan);
            $installments = $calculator->generateLoanInstallments();
            
            $this->assertCount($loan->getPeriod() + 1, $installments);
            /** @var Installment $installment */
            foreach ($installments as $i => $installment) {
                if ($i == 0) {
                    $expectedAmount = Money::create(0, $loan->getCurrencyCode());
                } elseif ($i == $loan->getPeriod()) {
                    $expectedAmount = Money::create($data['lastIAmount'], $loan->getCurrencyCode());
                } else {
                    $expectedAmount = Money::create($data['iAmount'], $loan->getCurrencyCode());                    
                }
                $this->assertEquals($expectedAmount, $installment->getAmount()->round(2));
            }
        }
    }
}
