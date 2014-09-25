<?php
namespace Zidisha\Borrower\Form;

use Propel\Runtime\Propel;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Form\Validator\NumberValidator;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Utility\Utility;

class AdminEditForm extends AbstractForm
{
    /**
     * @var \Zidisha\Borrower\Borrower
     */
    private $borrower;

    /**
     * @var $country
     */
    protected $country;

    protected $validatorClass = 'Zidisha\Borrower\Form\Validator\NumberValidator';

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    public function getRules($data)
    {

        $rules = [
            'firstName'                   => 'required',
            'lastName'                    => 'required',
            'email'                       => 'required|email|uniqueEmail:' . $this->borrower->getId(),
            'phoneNumber'                 => 'required|numeric|digits:' . $this->getPhoneNumberLength() . '|UniqueNumber:' . $this->borrower->getId() . '|MutualUniqueNumber',
            'alternatePhoneNumber'        => 'numeric|digits:' . $this->getPhoneNumberLength() . '|UniqueNumber:' . $this->borrower->getId() . '|MutualUniqueNumber',
            'address'                     => 'required',
            'addressInstructions'         => 'required',
            'city'                        => 'required|alpha_num_space',
            'countryId'                   => 'required',
            'communityLeader_firstName'   => 'required',
            'communityLeader_lastName'    => 'required',
            'communityLeader_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'communityLeader_description' => 'required',
            'familyMember_1_firstName'    => 'required',
            'familyMember_1_lastName'     => 'required',
            'familyMember_1_phoneNumber'  => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'familyMember_1_description'  => 'required',
            'familyMember_2_firstName'    => 'required',
            'familyMember_2_lastName'     => 'required',
            'familyMember_2_phoneNumber'  => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'familyMember_2_description'  => 'required',
            'familyMember_3_firstName'    => 'required',
            'familyMember_3_lastName'     => 'required',
            'familyMember_3_phoneNumber'  => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'familyMember_3_description'  => 'required',
            'neighbor_1_firstName'        => 'required',
            'neighbor_1_lastName'         => 'required',
            'neighbor_1_phoneNumber'      => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_1_description'      => 'required',
            'neighbor_2_firstName'        => 'required',
            'neighbor_2_lastName'         => 'required',
            'neighbor_2_phoneNumber'      => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_2_description'      => 'required',
            'neighbor_3_firstName'        => 'required',
            'neighbor_3_lastName'         => 'required',
            'neighbor_3_phoneNumber'      => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_3_description'      => 'required',
        ];

        if (!empty($data['password'])) {
            $rules = $rules + [
                    'password' => 'required',
                ];
        }

        return $rules;

    }

    protected function getPhoneNumberLength()
    {
        return $this->getCountry()->getPhoneNumberLength();
    }

    public function getCountry()
    {
        if ($this->country === null) {
            $this->country = $this->borrower->getCountry();
        }

        return $this->country;
    }

    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new NumberValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $rules);
    }

    public function getDefaultData()
    {
        $personalInformation = $this->borrower->getPersonalInformation() + [
                'firstName' => $this->borrower->getFirstName(),
                'lastName'  => $this->borrower->getLastName(),
                'email'     => $this->borrower->getUser()->getEmail(),
                'countryId' => $this->borrower->getCountryId(),
            ];

        return $personalInformation;
    }

    public function getDialingCode()
    {
        return '+ ' . $this->getCountry()->getDialingCode() . ' (0)';
    }

    public function getNestedData()
    {
        $data = $this->getData() + $this->borrower->getPersonalInformation();

        return Utility::nestedArray($data);
    }

    public function getBorrowerCountries()
    {
        $borrowerCountries = CountryQuery::create()
            ->filterByBorrowerCountry(1);

        $countries = [];

        foreach ($borrowerCountries as $country) {
            $countries[$country->getId()] = $country->getName();
        }
        return $countries;
    }
}
