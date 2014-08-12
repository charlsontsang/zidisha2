<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\VolunteerMentor as BaseVolunteerMentor;

class VolunteerMentor extends BaseVolunteerMentor
{

    const STATUS_PENDING_REVIEW = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DECLINED = 2;
    const STATUS_PENDING_VERIFICATION = -1;
    const STATUS_ASSIGNED_TO = -2;
}
