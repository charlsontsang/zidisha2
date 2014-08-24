<?php

use Zidisha\Admin\Form\ForgiveLoanForm;
use Zidisha\Country\CountryQuery;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;

class AdminLoanForgivenessController extends BaseController
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;
    /**
     * @var Zidisha\Admin\Form\ForgiveLoanForm
     */
    private $forgiveLoanForm;

    public function __construct(LoanService $loanService, ForgiveLoanForm $forgiveLoanForm)
    {
        $this->loanService = $loanService;
        $this->forgiveLoanForm = $forgiveLoanForm;
    }

    public function getIndex()
    {
        $page = Input::get('page', 1);

        $countryCode = Input::get('countryCode', 'KE');

        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        if (!$country) {
            \App::abort(404, 'wrong country code.');
        }

        $borrowerCountries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        $forgivenLoans = ForgivenLoanQuery::create()
            ->useBorrowerQuery()
            ->filterByCountry($country)
            ->endUse()
            ->orderByCreatedAt('DESC')
            ->paginate($page, 10);

        return View::make(
            'admin.loan-forgiveness.index',
            compact('forgivenLoans', 'borrowerCountries', 'countryCode')
        );
    }

    public function getAllow()
    {
        $countryCode = Input::get('countryCode', 'KE');

        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        if (!$country) {
            \App::abort(404, 'wrong country code.');
        }

        $borrowerCountries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find()
            ->toKeyValue('countryCode', 'name');

        $loans = LoanQuery::create()
            ->useBorrowerQuery()
            ->filterByCountry($country)
            ->endUse()
            ->filterByStatus(Loan::ACTIVE)
            ->find()
            ->toKeyValue('id', 'summary');

        return View::make('admin.loan-forgiveness.allow', compact('borrowerCountries', 'country', 'loans'));
    }

    public function postAllow($countryId)
    {
        $country = CountryQuery::create()
            ->findOneById($countryId);

        if (!$country) {
            \App::abort(404, 'wrong country code.');
        }

        $form = $this->forgiveLoanForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->loanService->allowLoanForgiveness($data);

            \Flash::success('Loan forgiven.');
            return Redirect::route('admin:allow-forgive-loan');
        }

        \Flash::error('Please enter valid inputs');
        return Redirect::back();
    }
} 