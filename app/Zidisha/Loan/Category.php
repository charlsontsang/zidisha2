<?php

namespace Zidisha\Loan;

use Zidisha\Loan\Base\Category as BaseCategory;

class Category extends BaseCategory
{
    public function setName($name) {
        parent::setName($name);
        $this->setSlug(str_replace(' ' , '-', strtolower($name)));

        return $this;
    }
}
