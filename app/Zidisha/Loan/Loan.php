<?php

namespace Zidisha\Loan;

use Zidisha\Loan\Base\Loan as BaseLoan;

class Loan extends BaseLoan
{

    const OPEN      = 0;
    const FUNDED    = 1;
    const ACTIVE    = 2;
    const REPAID    = 3;
    const DEFAULTED = 5;
    const CANCELED  = 6;
    const EXPIRED   = 7;

}
