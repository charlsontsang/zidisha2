<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Calculator\RescheduleCalculator;

class RescheduleLoanForm extends AbstractForm
{
    /**
     * @var \Zidisha\Loan\Calculator\RescheduleCalculator
     */
    private $rescheduleCalculator;

    public function __construct(RescheduleCalculator $rescheduleCalculator) {
        $this->rescheduleCalculator = $rescheduleCalculator;
    }

    public function getRules($data)
    {
        return [
            'installmentAmount' => 'required|numeric|min:' . $this->rescheduleCalculator->minInstallmentAmount()->getAmount(),
            'reason'            => 'required',
        ];
    }
}
