<?php

use Zidisha\Country\CountryQuery;

class BorrowController extends BaseController {


    public function getPage()
    {
        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        return View::make('pages.borrow', compact('countries'));
    }

}
