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

    public function getAllow($countryCode)
    {
        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        if (!$country) {
            \App::abort(404, 'wrong country code.');
        }
        
        $form = $this->allowLoanForgivenessForm;
        $form->setCountry($country);
        
        return View::make('admin.loan-forgiveness.allow', compact('borrowerCountries', 'form'));
    }

    public function postAllow()
    {
        $form = $this->allowLoanForgivenessForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $loan = LoanQuery::create()->findOneById($data['loanId']);
            $forgivenessLoan = $this->loanService->allowLoanForgiveness($loan, $data);

            \Flash::success('Allowed loan forgiveness.');
            return Redirect::route('admin:loan-forgiveness:index', $form->getCountry()->getCountryCode());
        }

        \Flash::error('Please enter valid inputs');
        $countryCode = \Input::get('countryCode', 'KE');
        $countryCode = $form->isValidCountryCode($countryCode) ? $countryCode : 'KE';
        return Redirect::route('admin:loan-forgiveness:allow', $countryCode)->withForm($form);
    }

    public function getLoans()
    {
        $form = $this->allowLoanForgivenessForm;
        $countryCode = Input::get('countryCode');

        if (!$form->isValidCountryCode($countryCode)) {
            App::abort('404');
        }
        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);

        $range = $form->getLoans($country);

        $options = [];
        foreach ($range as $k => $v) {
            $options[] = [$k, $v];
        }

        return Response::json($options);
    }
} 