<?php

use Zidisha\Admin\Form\AllowLoanForgivenessForm;
use Zidisha\Country\CountryQuery;
use Zidisha\Loan\ForgivenessLoanQuery;
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
     * @var Zidisha\Admin\Form\AllowLoanForgivenessForm
     */
    private $allowLoanForgivenessForm;

    public function __construct(LoanService $loanService, AllowLoanForgivenessForm $allowLoanForgivenessForm)
    {
        $this->loanService = $loanService;
        $this->allowLoanForgivenessForm = $allowLoanForgivenessForm;
    }

    public function getIndex($countryCode = 'KE')
    {
        $page = Input::get('page', 1);

        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        if (!$country) {
            \App::abort(404, 'wrong country code.');
        }

        $borrowerCountries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        $forgivenessLoans = ForgivenessLoanQuery::create()
            ->useBorrowerQuery()
                ->filterByCountry($country)
            ->endUse()
            ->orderByCreatedAt('DESC')
            ->paginate($page, 20);

        return View::make(
            'admin.loan-forgiveness.index',
            compact('forgivenessLoans', 'borrowerCountries', 'countryCode')
        );
    }

    public function getAllow()
    {
        $form = $this->allowLoanForgivenessForm;
        
        return View::make('admin.loan-forgiveness.allow', compact('borrowerCountries', 'form'));
    }

    public function postAllow()
    {
        $form = $this->allowLoanForgivenessForm;
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