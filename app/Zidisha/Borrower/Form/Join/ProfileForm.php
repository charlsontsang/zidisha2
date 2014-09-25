<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\CategoryQuery;

class ProfileForm extends AbstractForm
{
    protected $country;
    protected $cities;
    protected $isSaveLater;
    
    protected $validatorClass = 'Zidisha\Borrower\Form\Validator\NumberValidator';

    public function getRules($data)
    {
        $phoneNumberLength = $this->getCountry()->getPhoneNumberLength();

        if ($this->isSaveLater) {
            return [
                'email' => 'required|email|unique:users,email',
            ];
        }

        return [
            'username'                 => 'required|unique:users,username',
            'password'                 => 'required|min:8',
            'email'                    => 'required|email|unique:users,email',
            'preferredLoanAmount'      => 'required',
            'preferredInterestRate'    => 'required',
            'preferredRepaymentAmount' => 'required',
            'businessCategoryId'       => 'required|in:' . implode(',', array_keys($this->getCategories())),
            'businessYears'            => 'required|in:' . implode(',', array_keys($this->getBusinessYears())),
            'loanUsage'                => 'required|in:' . implode(',', array_keys($this->getLoanUsage())),
//            'birthDate'                => 'required',
            'firstName'                => 'required',
            'lastName'                 => 'required',
            'address'                  => 'required',
            'addressInstructions'      => 'required',
            'city'                     => 'required',
            'nationalIdNumber'         => 'required|unique:borrower_profiles,national_id_number',
            'phoneNumber'              => 'required|numeric|digits:' . $phoneNumberLength . '|UniqueNumber|MutualUniqueNumber',
            'alternatePhoneNumber'     => 'numeric|digits:' . $phoneNumberLength . '|UniqueNumber|MutualUniqueNumber',
            'communityLeader_firstName'    => 'required',
            'communityLeader_lastName'     => 'required',
            'communityLeader_phoneNumber'  => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'communityLeader_description'  => 'required',
            'familyMember_1_firstName'     => 'required',
            'familyMember_1_lastName'      => 'required',
            'familyMember_1_phoneNumber'   => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'familyMember_1_description'   => 'required',
            'familyMember_2_firstName'     => 'required',
            'familyMember_2_lastName'      => 'required',
            'familyMember_2_phoneNumber'   => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'familyMember_2_description'   => 'required',
            'familyMember_3_firstName'     => 'required',
            'familyMember_3_lastName'      => 'required',
            'familyMember_3_phoneNumber'   => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'familyMember_3_description'   => 'required',
            'neighbor_1_firstName'         => 'required',
            'neighbor_1_lastName'          => 'required',
            'neighbor_1_phoneNumber'       => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'neighbor_1_description'       => 'required',
            'neighbor_2_firstName'         => 'required',
            'neighbor_2_lastName'          => 'required',
            'neighbor_2_phoneNumber'       => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'neighbor_2_description'       => 'required',
            'neighbor_3_firstName'         => 'required',
            'neighbor_3_lastName'          => 'required',
            'neighbor_3_phoneNumber'       => 'required|numeric|ContactUniqueNumber|digits:' . $phoneNumberLength,
            'neighbor_3_description'       => 'required',
            'volunteerMentorCity'          => 'required|in:' . implode(',', array_keys($this->getVolunteerMentorCities())),
            'volunteerMentorId'            => 'required|in:' . implode(
                    ',',
                    array_keys(VolunteerMentorQuery::create()->getVolunteerMentorsByCity($data['volunteerMentorCity']))
                ),
        ];
    }

    public function getDefaultData()
    {
        return [
            'email' => \Session::get('BorrowerJoin.email'),
        ];
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        if ($this->country === null) {
            $this->country = CountryQuery::create()
                ->findOneByCountryCode(\Session::get('BorrowerJoin.countryCode'));
        }

        return $this->country;
    }

    public function getVolunteerMentorCities()
    {
        if ($this->cities === null) {
            $this->cities = VolunteerMentorQuery::create()
                ->getVolunteerMentorCities($this->getCountry());
        }
        
        return $this->cities;
    }

    public function getDialingCode()
    {
        return '+ ' . $this->getCountry()->getDialingCode() . ' (0)';
    }

    public function getBorrowersByCountry()
    {
        $list = [];
        $list[0] = null;
        $countryCode = \Session::get('BorrowerJoin.countryCode');
        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        $borrowers = BorrowerQuery::create()
            ->filterByCountry($country)
            ->orderByFirstName()
            ->filterByActive(true)
            ->joinWith('Profile')
            ->find();

        foreach ($borrowers as $borrower) {
            $list[$borrower->getId()] = $borrower->getName() . " ( " . $borrower->getProfile()->getCity() . " )";
        }

        return $list;
    }

    public function getVolunteerMentors()
    {
        $city = \Input::old('volunteerMentorCity');
        if ($city === null) {
            $cities = $this->getVolunteerMentorCities();
            $city = $cities ? reset($cities) : null;
        }

        return $city ? VolunteerMentorQuery::create()->getVolunteerMentorsByCity($city) : [];
    }

    public function setIsSaveLater($state = true)
    {
        $this->isSaveLater = $state;
    }

    public function getIsSaveLater()
    {
        return $this->isSaveLater;
    }

    public function getCategories()
    {
        $categories = CategoryQuery::create()
            ->orderBySortableRank()
            ->findByAdminOnly(false);

        return $categories->toKeyValue('id', 'name');
    }

    public function getBusinessYears()
    {
        return [
            '0' => 'Less than a year',
            '1' => ' 1 - 2 years',
            '2' => '2 - 5 years',
            '3' => '5 - 10 years',
            '4' => 'More than 10 years',
        ];
    }

    public function getLoanUsage()
    {
        return [
            '0' => 'Inventory',
            '1' => 'Equipment',
            '2' => 'Livestock',
            '3' => 'School fees',
            '4' => 'Hospital Fees',
            '5' => 'Home renovation',
            '6' => 'Repay another loan',
            '7' => 'Other',
        ];
    }
}
