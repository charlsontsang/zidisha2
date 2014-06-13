<?php

namespace Zidisha\Country;

use Zidisha\Country\Base\Country as BaseCountry;

class Country extends BaseCountry
{
    public function setName($name) {
        parent::setName($name);
        $this->setSlug(str_replace(' ' , '-', strtolower($name)));

        return $this;
    }
}
