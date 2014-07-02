<?php

use Zidisha\Admin\Form\ExchangeRateForm;
use Zidisha\Admin\Form\FilterBorrowers;
use Zidisha\Admin\Form\FilterLenders;
use Zidisha\Admin\Form\FilterLoans;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Currency\CurrencyService;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Loan\Loan;

class AdminController extends BaseController
{

    protected $lenderQuery, $borrowerQuery, $countryQuery;
    protected $borrowersForm, $lendersForm, $loansForm;
    private $loanService;
    private $adminService;
    private $exchangeRateForm;
    private $currencyService;

    public function  __construct(
        LenderQuery $lenderQuery,
        BorrowerQuery $borrowerQuery,
        CountryQuery $countryQuery,
        FilterBorrowers $borrowersForm,
        FilterLenders $lendersForm,
        FilterLoans $loansForm,
        LoanService $loanService,
        CurrencyService $currencyService,
        ExchangeRateForm $exchangeRateForm
    ) {
        $this->lenderQuery = $lenderQuery;
        $this->$borrowerQuery = $borrowerQuery;
        $this->countryQuery = $countryQuery;
        $this->borrowersForm = $borrowersForm;
        $this->lendersForm = $lendersForm;
        $this->loansForm = $loansForm;
        $this->loanService = $loanService;
        $this->exchangeRateForm = $exchangeRateForm;
        $this->currencyService = $currencyService;
    }

    public
    function getDashboard()
    {
        return View::make('admin.dashboard');
    }

    public function getBorrowers()
    {
        $page = Request::query('page') ? : 1;
        $countryId = Request::query('country') ? : null;
        $email = Request::query('email') ? : null;

        $query = BorrowerQuery::create();

        if ($countryId) {
            $query->filterByCountryId($countryId);
        }
        if ($email) {
            $query
                ->useUserQuery()
                ->filterByEmail($email)
                ->endUse();
        }

        $paginator = $query
            ->orderById()
            ->paginate($page, 3);

        return View::make('admin.borrowers', compact('paginator'), ['form' => $this->borrowersForm,]);
    }

    public function getLenders()
    {
        $page = Request::query('page') ? : 1;
        $countryId = Request::query('country') ? : null;
        $email = Request::query('email') ? : null;

        $query = LenderQuery::create();

        if ($countryId) {
            $query->filterByCountryId($countryId);
        }
        if ($email) {
            $query
                ->useUserQuery()
                ->filterByEmail($email)
                ->endUse();
        }

        $paginator = $query
            ->orderById()
            ->paginate($page, 3);

        return View::make('admin.lenders', compact('paginator'), ['form' => $this->borrowersForm,]);
    }

    public function getLoans()
    {
        $page = Request::query('page') ? : 1;
        $countryName = Request::query('country') ? : null;
        $status = Request::query('status') ? : null;

        $selectedCountry = $this->countryQuery->findOneBySlug($countryName);

        $conditions = [];

        $routeParams = [
            'stage' => 'fund-raising',
            'country' => 'everywhere'
        ];

        if ($selectedCountry) {
            $conditions['countryId'] = $selectedCountry->getId();
            $routeParams['country'] = $selectedCountry->getSlug();
        }

        if ($status) {
            if ($status == 'completed') {
                $routeParams['stage'] = 'completed';
                $conditions['status'] = [Loan::DEFAULTED, Loan::REPAID];
            } elseif ($status == 'active') {
                $routeParams['stage'] = 'active';
                $conditions['status'] = [Loan::ACTIVE, Loan::FUNDED];
            } else {
                $routeParams['stage'] = 'fund-raising';
                $conditions['status'] = Loan::OPEN;
            }
        }

        $paginator = $this->loanService->searchLoans($conditions, $page);

        return View::make('admin.loans', compact('paginator'), ['form' => $this->loansForm,]);
    }

    public function getExchangeRates($countrySlug = null)
    {
        $page = Request::query('page') ? : 1;
        $rates = $this->currencyService->getExchangeRatesForCountry($countrySlug);

        $paginator = $rates
            ->paginate($page, 50);
        $offset = ($page - 1) * 50;

        return View::make('admin.exchange-rates', compact('paginator', 'countrySlug', 'offset'),
            ['form' => $this->exchangeRateForm,]);
    }

    public function postExchangeRates()
    {
        $form = $this->exchangeRateForm;
        $form->handleRequest(Request::instance());
        $data = $form->getData();
        $countrySlug = array_get($data, 'countrySlug');

        if ($form->isValid()) {

            $this->currencyService->updateExchangeRateForCountry($data);

            \Flash::success("Exchange rate Successfully updated!");
            return Redirect::route('admin:exchange-rates', $countrySlug);
        }

        return Redirect::route('admin:exchange-rates', $countrySlug)->withForm($form);
    }

    public function getPendingActivation()
    {
        $page = Request::query('page') ?: 1;
        
        $paginator = BorrowerQuery::create()
            ->filterByVerified(true)
            ->orderByCreatedAt() // Todo registration date
            ->paginate($page, 50);

        return View::make('admin.pending-activation', compact('paginator'));
    }
}
