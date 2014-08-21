<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Profile as BaseProfile;

class Profile extends BaseProfile
{
    const LESS_THAN_A_YEAR = 0;
    const ONE_TO_TWO_YEARS = 1;
    const TWO_TO_FIVE_YEARS = 2;
    const FIVE_TO_TEN_YEARS = 3;
    const MORE_THAN_TEN_YEARS = 4;

    const INVENTORY = 0;
    const EQUIPMENT = 1;
    const LIVESTOCK = 2;
    const SCHOOL_FEES = 3;
    const HOSPITAL_FEES = 4;
    const HOME_RENOVATION = 5;
    const REPAY_ANOTHER_LOAN = 6;
    const OTHER = 7;
}
