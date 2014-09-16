<?php

use Zidisha\Country\CountryQuery;
use Zidisha\Country\Form\EditForm;
use Zidisha\Currency\Money;

class CountryController extends BaseController
{
    /**
     * @var Zidisha\Country\Form\EditForm
     */
    private $editForm;

    public function __construct(EditForm $form)
    {
        $this->editForm = $form;
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
        $country = CountryQuery::create()
            ->findOneById($id);

        if (!$country) {
            \App::abort(404, 'fatal error.');
        }

        $form = $this->editForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $country
                ->setBorrowerCountry($data['borrower_country'])
                ->setDialingCode($data['dialing_code'])
                ->setPhoneNumberLength($data['phone_number_length'])
                ->setRegistrationFee(Money::create($data['registration_fee'], $country->getCurrencyCode()))
                ->setInstallmentAmountStep($data['installment_amount_step'])
                ->setLoanAmountStep($data['loan_amount_step'])
                ->setInstallmentPeriod($data['installment_period'])
                ->setRepaymentInstructions($data['repayment_instructions'])
                ->setAcceptBidsNote($data['accept_bids_note']);

            $country->save();

            \Flash::success('Changes have been updated.');
            return Redirect::route('admin:edit:country', $id);
        }

        \Flash::error('Please use proper options.');
        return Redirect::route('admin:edit:country', $id)->withForm($form);
    }
}
