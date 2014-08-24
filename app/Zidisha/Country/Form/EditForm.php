<?php
namespace Zidisha\Country\Form;

use Zidisha\Country\Country;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Loan;

class EditForm extends AbstractForm
{
    /**
     * @var \Zidisha\Country\Country
     */
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getDefaultData()
    {
        return [
            'borrower_country'       => $this->country->getBorrowerCountry(),
            'dialing_code'           => $this->country->getDialingCode(),
            'phone_number_length'    => $this->country->getPhoneNumberLength(),
            'registration_fee'       => $this->country->getRegistrationFee()->getAmount(),
            'installment_amount_step'=> $this->country->getInstallmentAmountStep(),
            'loan_amount_step'       => $this->country->getLoanAmountStep(),
            'installment_period'     => $this->country->getInstallmentPeriod(),
            'repayment_instructions' => $this->country->getRepaymentInstructions(),
            'accept_bids_note'       => $this->country->getAcceptBidsNote(),
        ];
    }

    public function getRules($data)
    {
        return [
            'borrower_country'       => 'required',
            'dialing_code'           => 'required|numeric|digits_between:1,3',
            'phone_number_length'    => 'required|numeric',
            'registration_fee'       => 'required|numeric',
            'installment_amount_step'=> 'required|numeric',
            'loan_amount_step'       => 'required|numeric',
            'installment_period'     => 'required',
            'repayment_instructions' => 'required_if:borrower_country,1',
            'accept_bids_note'       => ''
        ];
    }

    public function getInstallmentPeriods()
    {
        return [
            'weekly'  => Loan::WEEKLY_INSTALLMENT,
            'monthly' => Loan::MONTHLY_INSTALLMENT
        ];
    }

    public function getDefaultInstallmentPeriod()
    {
        return $this->country->getInstallmentPeriod();
    }

    public function getCurrency()
    {
        return $this->country->getCurrencyCode();
    }
}
