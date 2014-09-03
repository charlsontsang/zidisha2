<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\Follower as BaseFollower;

class Follower extends BaseFollower
{

    protected $isFunded = false;
    
    public function setIsFunded()
    {
        $this->isFunded = true;
        
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFunded()
    {
        return $this->isFunded;
    }
}
