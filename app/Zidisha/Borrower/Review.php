<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Review as BaseReview;

class Review extends BaseReview
{
    public function isCompleted()
    {
        return $this->getIsAddressLocatable();
    }
}
