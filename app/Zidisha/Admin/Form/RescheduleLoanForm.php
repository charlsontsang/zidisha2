<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Calculator\RescheduleCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\RepaymentSchedule;

class RescheduleLoanForm extends AbstractForm
{
    /**
     * @var \Zidisha\Loan\Calculator\RescheduleCalculator
     */
    private $rescheduleCalculator;

    public function __construct(Loan $loan, RepaymentSchedule $repaymentSchedule) {
        $this->rescheduleCalculator = new RescheduleCalculator($loan, $repaymentSchedule);
    }

    public function getRules($data)
    {
        return [
            'installmentAmount' => 'required|numeric|min:' . $this->getMinInstallmentAmount(),
            'reason'            => 'required|min:500',
        ];
    }

    public function getDefaultData()
    {   
        return \Session::get('reschedule', []);
    }

    /**
     * @return string
     */
    public function getMinInstallmentAmount()
    {
        return $this->rescheduleCalculator->minInstallmentAmount()->getAmount();
    }
}
