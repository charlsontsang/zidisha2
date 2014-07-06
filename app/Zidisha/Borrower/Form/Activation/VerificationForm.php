<?php
namespace Zidisha\Borrower\Form\Activation;


use Zidisha\Borrower\Borrower;
use Zidisha\Form\AbstractForm;

class VerificationForm extends AbstractForm
{

    /**
     * @var Borrower
     */
    private $borrower;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    public function getRules($data)
    {
        return [
            'isEligibleByAdmin' => 'required|in:0;1',
        ];
    }

    public function getDefaultData()
    {        
        return [
            'isEligibleByAdmin' => $this->borrower->isActivationApproved(),
        ];
    }
}
