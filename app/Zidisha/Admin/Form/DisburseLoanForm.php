<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Loan;

class DisburseLoanForm extends AbstractForm
{
    /**
     * @var Loan
     */
    private $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function getRules($data)
    {
        $rules =  [
            'disbursedAt'     => 'required|date_format:m/d/Y',
            'disbursedAmount' => 'required|numeric',
        ];
        
        if ($this->loan->getRegistrationFee()->isPositive()) {
            $rules['registrationFee'] = 'required|numeric';
        }
        
        return $rules;
    }
}
