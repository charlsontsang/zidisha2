<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Loan;

class RescheduleLoanForm extends AbstractForm
{

    /**
     * @var \Zidisha\Loan\Loan
     */
    private $loan;

    public function __construct(Loan $loan) {
        $this->loan = $loan;
    }

    public function getRules($data)
    {
        return [
            'installmentAmount' => 'required|numeric|greaterThan:0',
            'reason'            => 'required',
        ];
    }
}
