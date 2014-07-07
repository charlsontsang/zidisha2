<?php

use Zidisha\Country\CountryQuery;
use Zidisha\Country\Form\EditForm;

class CountryController extends BaseController
{
    /**
     * @var Zidisha\Country\Form\EditForm
     */
    private $form;

    public function __construct(EditForm $form)
    {
        $this->form = $form;
    }

    public function getCountries()
    {
        $otherCountries = Input::get('other_countries');

        $countries = CountryQuery::create();

        $otherCountries ? $countries->filterByBorrowerCountry(0) : $countries->filterByBorrowerCountry(1);

        $countries->find();

        return View::make('admin.country.index', compact('countries'));
    }

}
