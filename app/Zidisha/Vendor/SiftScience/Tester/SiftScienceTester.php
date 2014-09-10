<?php

namespace Zidisha\Vendor\SiftScience\Tester;

use Zidisha\User\User;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class SiftScienceTester {

    private $siftScienceService;

    public function __construct(SiftScienceService $siftScienceService)
    {
        $this->siftScienceService = $siftScienceService;
    }

    public function loanArrearLabel()
    {
        $user = new User();
        $user->setId(5);
        $loanId = 10;

        $this->siftScienceService->loanArrearLabel($user, $loanId);
    }
}
