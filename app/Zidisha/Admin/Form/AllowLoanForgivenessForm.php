<?php

namespace Zidisha\Admin\Form;

use Propel\Runtime\ActiveQuery\Criteria;
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
            'loanId'      => 'required|in:' . implode(',', array_keys($this->getLoans())),
            'comment'     => 'required'
        ];
    }

    public function getDefaultDate()
    {
        return [
            'countryCode' => $this->getCountry()->getCountryCode(),
        ];
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        if (!$this->country) {
            $countryCode = \Input::get('countryCode', 'KE');
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

    public function getLoans(Country $country = null)
    {
        $_loans = LoanQuery::create()
            ->useBorrowerQuery()
                ->filterByCountryId($country ? $country->getId() : $this->getCountry()->getId())
            ->endUse()
            ->useForgivenessLoanQuery(null, Criteria::LEFT_JOIN)
                ->filterByLoanId(null, Criteria::ISNULL)
            ->endUse()
            ->filterByStatus(Loan::ACTIVE)
            ->find();

        $loans = [];
        foreach($_loans as $loan) {
            $loans[$loan->getId()] = $loan->getBorrower()->getName() . ' (' . $loan->getBorrower()->getProfile()->getCity() . ')';
        }
        
        return $loans;
    }

    public function isValidCountryCode($countryCode)
    {
        $data = compact('countryCode');
        $rules = $this->getRules($data);
        $validator = \Validator::make($data, ['countryCode' => $rules['countryCode']]);

        return $validator->passes();
    }
}

