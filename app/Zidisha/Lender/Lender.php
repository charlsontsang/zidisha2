<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\Lender as BaseLender;

class Lender extends BaseLender
{
    public function getName(){
        return $this->getFirstName() . " " . $this->getLastName();
    }
}
