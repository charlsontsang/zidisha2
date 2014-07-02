<?php
namespace Zidisha\Borrower\Form\Join;

use Illuminate\Routing\Route;
use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Borrower\Form\Validator\NumberValidator;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class ProfileForm extends AbstractForm
{
    protected $country;
    protected $cities;

    public function getRules($data)
    {
        $phoneNumberLength = $this->getCountry()->getPhoneNumberLength();

        return [
            'username'             => 'required|unique:users,username',
            'password'             => 'required',
            'email'                => 'required|email|unique:users,email',
            'firstName'            => 'required',
            'lastName'             => 'required',
            'address'              => 'required',
            'addressInstruction'   => 'required',
            'city'                 => 'required',
            'nationalIdNumber'     => 'required|unique:borrower_profiles,national_id_number',
            'phoneNumber'          => 'required|numeric|digits:' . $phoneNumberLength .'|UniqueNumber|MutualUniqueNumber',
            'alternatePhoneNumber' => 'numeric|digits:' . $phoneNumberLength . '|UniqueNumber|MutualUniqueNumber',
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
                    array_keys(VolunteerMentorQuery::create()->getVolunteerMentorByCity($data['volunteerMentorCity']))
                ),
            'members'                      => 'in:' . implode(',', array_keys($this->getBorrowersByCountry())),
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
            $country = $this->getCountry();

            $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
            $sql = "SELECT DISTINCT city FROM borrower_profiles WHERE borrower_id IN "
                . "(SELECT borrower_id FROM volunteer_mentors WHERE country_id = :country_id AND status = :status
            AND mentee_count < :mentee_count)";
            $stmt = $con->prepare($sql);
            //TODO to make mentee_count = 50
            $stmt->execute(array(':country_id' => $country->getId(), ':status' => '1', ':mentee_count' => '25'));
            $cities = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $this->cities = array_combine($cities, $cities);
        }

        return $this->cities;
    }

    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new NumberValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $this->getRules($data));
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

        return $city ? VolunteerMentorQuery::create()->getVolunteerMentorByCity($city) : [];
    }

}
