<?php

namespace Zidisha\Generate;


use Carbon\Carbon;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Borrower\VolunteerMentorQuery;

class BorrowerGenerator extends Generator
{
    /**
     * @var \Zidisha\Borrower\BorrowerService
     */
    protected $borrowerService;

    protected $joinedAtStartDate = '- 16 months';
    protected $joinedAtEndDate = 'now';

    protected $volunteerMentor = false;
    protected $verified = true;

    protected $countryIds;
    protected $cities;
    protected $borrowerCount;
    protected $volunteerMentorIdsByCountryId;

    public function __construct(BorrowerService $borrowerService)
    {
        $this->borrowerService = $borrowerService;
    }

    public function joinedAtBetween($startDate = '- 16 months', $endDate = 'now')
    {
        $this->joinedAtStartDate = $startDate;
        $this->joinedAtEndDate = $endDate;
        
        return $this;
    }

    public function volunteerMentor($enabled = false)
    {
        $this->volunteerMentor = $enabled;
        
        return $this;
    }

    public function verified($verified = false)
    {
        $this->verified = $verified;

        return $this;
    }

    protected function beforeGenerate()
    {
        $this->countryIds = range(1, 6, 1);
        $this->borrowerCount = BorrowerQuery::create()->count();

        $this->cities = [];
        foreach ($this->countryIds as $countryId) {
            for ($i = 0; $i < 5; $i++) {
                $this->cities[$countryId][] = $this->faker->city;
            }
        }

        $volunteerMentors = VolunteerMentorQuery::create()
            ->select(['BorrowerId', 'CountryId'])
            ->find()
            ->getData();
        
        $this->volunteerMentorIdsByCountryId = [];
        foreach ($this->countryIds as $countryId) {
            $this->volunteerMentorIdsByCountryId[$countryId] = [];
        }
        
        foreach ($volunteerMentors as $volunteerMentor) {
            $this->volunteerMentorIdsByCountryId[$volunteerMentor['CountryId']][] = $volunteerMentor['BorrowerId'];
        }
    }

    protected function doGenerate($i)
    {
        $number = $this->borrowerCount + $i;
        $countryId = $this->faker->randomElement($this->countryIds);

        $data = [
            'volunteerMentorId'    => $this->faker->randomElement($this->volunteerMentorIdsByCountryId[$countryId]),
            'referrerId'           => null,
            // User
            'joinedAt'             => $this->faker->dateTimeBetween($this->joinedAtStartDate, $this->joinedAtEndDate),
            'username'             => 'borrower' . $number,
            'password'             => '1234567890',
            'email'                => 'borrower' . $number . '@mail.com',
            'facebookId'           => 1000000000 + $number,
            // Borrower
            'firstName'            => 'Borrower' . $number,
            'lastName'             => $this->faker->lastName,
            'countryId'            => $countryId,
            // Profile
            'address'              => $this->faker->paragraph(3),
            'addressInstructions'  => $this->faker->paragraph(6),
            'city'                 => $this->faker->randomElement($this->cities[$countryId]),
            'nationalIdNumber'     => $this->faker->randomNumber(10),
            'phoneNumber'          => $this->faker->numberBetween(100000000, 1000000000),
            'alternatePhoneNumber' => $this->faker->numberBetween(100000000, 1000000000),
            // Contacts
            'communityLeader'      => $this->generateContact(),
            'familyMember'         => [
                1 => $this->generateContact(),
                2 => $this->generateContact(),
                3 => $this->generateContact(),
            ],
            'neighbor'             => [
                1 => $this->generateContact(),
                2 => $this->generateContact(),
                3 => $this->generateContact(),
            ],
            // JoinLog
            'ipAddress'            => $this->faker->ipv4,
        ];

        $borrower = $this->borrowerService->joinBorrower($data);
        // TODO
        $borrower->setActivationStatus(Borrower::ACTIVATION_APPROVED);
        $borrower->save();
        
        if ($this->verified) {
            $verifiedAt = Carbon::instance($borrower->getUser()->getJoinedAt());
            $verifiedAt->addHour();
            $this->borrowerService->verifyBorrower($borrower, $verifiedAt);
        }
        
        if ($this->volunteerMentor) {
            $user = $borrower->getUser();
            $user->setSubRole('volunteerMentor');
            $user->save();

            $mentor = new VolunteerMentor();
            $mentor
                ->setBorrowerVolunteer($borrower)
                ->setCountryId($borrower->getCountryId())
                ->setStatus(1) // TODO ??
                ->setGrantDate(new \DateTime()); // TODO rename
            $mentor->save();
        }
        
        return $borrower;
    }

    /**
     * @return array
     */
    protected function generateContact()
    {
        return [
            'phoneNumber' => $this->faker->numberBetween(100000000, 1000000000),
            'firstName'   => $this->faker->firstName,
            'lastName'    => $this->faker->lastName,
            'description' => $this->faker->sentence(5),
        ];
    }
}
