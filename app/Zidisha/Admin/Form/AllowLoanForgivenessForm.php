<?php

namespace Zidisha\Admin\Form;

use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;

class AllowLoanForgivenessForm extends AbstractForm
{

    /**
     * @var Country $country
     */
    protected $country;

    public function getRules($data)
    {
        return [
            'countryCode' => 'required|exists:countries,country_code,borrower_country,1',
            'loanId'      => 'required|exists:loans,id',
            'comment'     => ''
        ];
    }

    public function getCountry()
    {
        if (!$this->country) {
            $countryCode = array_get($this->getData(), 'countryCode', 'KE');
            $this->country = CountryQuery::create()
                ->findOneByCountryCode($countryCode);
        }

        return $this->country;
    }
    
    public function getCountries()
    {
        return CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find()
            ->toKeyValue('countryCode', 'name');
    }

    public function getLoans()
    {
        $_loans = LoanQuery::create()
            ->useBorrowerQuery()
            ->filterByCountry($this->getCountry())
            ->endUse()
            ->filterByStatus(Loan::ACTIVE)
            ->find();

        $loans = [];
        foreach($_loans as $loan) {
            $loans[$loan->getId()] = $loan->getBorrower()->getName() . ' (' . $loan->getBorrower()->getProfile()->getCity() . ')';
        }
        
        return $loans;
    }
}

