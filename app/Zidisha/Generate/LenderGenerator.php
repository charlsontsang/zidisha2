<?php

namespace Zidisha\Generate;


use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\User\User;

class LenderGenerator extends Generator
{
    /**
     * @var \Zidisha\Lender\LenderService
     */
    protected $lenderService;

    protected $joinedAtStartDate = '- 16 months';
    protected $joinedAtEndDate = 'now';
    protected $volunteer = false;
    
    protected $countryIds;
    protected $lenderCount;

    public function __construct(LenderService $lenderService)
    {
        $this->lenderService = $lenderService;
    }

    public function joinedAtBetween($startDate = '- 16 months', $endDate = 'now')
    {
        $this->joinedAtStartDate = $startDate;
        $this->joinedAtEndDate = $endDate;
        
        return $this;
    }

    public function volunteer($enabled = false)
    {
        $this->volunteer = $enabled;

        return $this;
    }

    protected function beforeGenerate()
    {
        $this->countryIds = range(1, 7, 1);
        $this->lenderCount = LenderQuery::create()->count();
    }

    protected function doGenerate($i)
    {
        $number = $this->lenderCount + $i;
        $data = [
            'firstName' => 'Lender' . $number,
            'lastName'  => $this->faker->lastName,
            'aboutMe'   => $this->faker->paragraph(),
            'username'  => 'lender' . $number,
            'password'  => '1234567890',
            'email'     => 'lender' . $number . '@mail.com',
            'countryId' => $this->faker->randomElement($this->countryIds),
            'joinedAt'  => $this->faker->dateTimeBetween($this->joinedAtStartDate, $this->joinedAtEndDate),
        ];

        $lender = $this->lenderService->joinLender($data);

        if ($this->volunteer) {
            $user = $lender->getUser();
            $user->setSubRole(User::SUB_ROLE_VOLUNTEER);
            $user->save();
        }

        return $lender;
    }
}
