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

        return View::make('admin.country.index', compact('countries', 'otherCountries'));
    }

    public function editCountry($id)
    {
        if (!$id) {
            \App::abort(404, 'fatal error.');
        }

        $country = CountryQuery::create()
            ->findOneById($id);

        if (!$country) {
            \App::abort(404, 'fatal error.');
        }

        $form = new EditForm($country);

        return View::make('admin.country.edit', compact('form', 'country'));
    }

    public function postEditCountry($id)
    {

        if (!$id) {
            \App::abort(404, 'fatal error.');
        }


        $form = $this->form;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $country = CountryQuery::create()
                ->findOneById($id);

            if (!$country) {
                \App::abort(404, 'fatal error.');
            }

            $data = $form->getData();

            $country
                ->setBorrowerCountry($data['borrower_country'])
                ->setDialingCode($data['dialing_code'])
                ->setPhoneNumberLength($data['phone_number_length'])
                ->setRegistrationFee($data['registration_fee'])
                ->setInstallmentPeriod($data['installment_period'])
                ->setRepaymentInstructions($data['repayment_instructions']);

            $country->save();

            \Flash::success('Changes have been updated.');
            return Redirect::route('admin:edit:country', $id);
        }

        \Flash::error('Please use proper options.');
        return Redirect::route('admin:edit:country', $id)->withForm($form);
    }
}
