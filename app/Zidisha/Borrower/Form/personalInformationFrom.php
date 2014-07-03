<?php
namespace Zidisha\Borrower\Form;

use Propel\Runtime\Propel;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Form\Validator\NumberValidator;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class personalInformationFrom extends AbstractForm
{

    protected $editableField;

    /**
     * @var \Zidisha\Borrower\Borrower
     */
    private $borrower;

    /**
     * @var $country
     */
    protected $country;

    /**
     * @var $cities
     */
    protected $cities;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;

        $this->checkPersonalInformation();
    }

    public function getRules($data)
    {
        return [
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
        ];
    }

    public function getAllRules()
    {
        return [
            'address' => 'required',
            'addressInstruction' => 'required',
            'city' => 'required',
            'nationalIdNumber' => 'required|unique:borrower_profiles,national_id_number',
            'phoneNumber' => 'required|numeric|digits:' . $this->getPhoneNumberLength(
                ) . '|UniqueNumber|MutualUniqueNumber',
            'alternatePhoneNumber' => 'numeric|digits:' . $this->getPhoneNumberLength(
                ) . '|UniqueNumber|MutualUniqueNumber',
            'communityLeader_firstName' => 'required',
            'communityLeader_lastName' => 'required',
            'communityLeader_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(
                ),
            'communityLeader_description' => 'required',
            'familyMember_1_firstName' => 'required',
            'familyMember_1_lastName' => 'required',
            'familyMember_1_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(
                ),
            'familyMember_1_description' => 'required',
            'familyMember_2_firstName' => 'required',
            'familyMember_2_lastName' => 'required',
            'familyMember_2_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(
                ),
            'familyMember_2_description' => 'required',
            'familyMember_3_firstName' => 'required',
            'familyMember_3_lastName' => 'required',
            'familyMember_3_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(
                ),
            'familyMember_3_description' => 'required',
            'neighbor_1_firstName' => 'required',
            'neighbor_1_lastName' => 'required',
            'neighbor_1_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_1_description' => 'required',
            'neighbor_2_firstName' => 'required',
            'neighbor_2_lastName' => 'required',
            'neighbor_2_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_2_description' => 'required',
            'neighbor_3_firstName' => 'required',
            'neighbor_3_lastName' => 'required',
            'neighbor_3_phoneNumber' => 'required|numeric|ContactUniqueNumber|digits:' . $this->getPhoneNumberLength(),
            'neighbor_3_description' => 'required',
        ];
    }

    protected function getPhoneNumberLength()
    {
        return $this->borrower->getCountry()->getPhoneNumberLength();
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

        return $city ? VolunteerMentorQuery::create()->getVolunteerMentorByCity($city) : [];
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

    public function getCountry()
    {
        if ($this->country === null) {
            $this->country = CountryQuery::create()
                ->findOneByCountryCode(\Session::get('BorrowerJoin.countryCode'));
        }

        return $this->country;
    }

    public function checkPersonalInformation()
    {
        $rules = $this->getAllRules();

        $this->validate($this->borrower->getPersonalInformation(), $rules);

        $messages = $this->getMessageBag()->getMessages();

        $fields = array_combine(array_keys($rules), array_fill(0, count($rules), false));

        foreach ($messages as $field => $error) {
            $fields[$field] = true;
            preg_match('/^(?P<contact>.*_)(firstName|lastName)$/', $field, $matches);

            if ($matches) {
                $fields[$matches['contact'] . 'firstName'] = true;
                $fields[$matches['contact'] . 'lastName'] = true;
            }
        }

//        dd($fields);
        $this->editableField = $fields;
    }

    public function isEditable($fieldName)
    {
        return $this->editableField[$fieldName];
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

}