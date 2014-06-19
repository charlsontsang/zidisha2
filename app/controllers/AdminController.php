<?php

use Zidisha\Admin\Form\FilterBorrowers;
use Zidisha\Admin\Form\FilterLenders;
use Zidisha\Admin\Form\FilterLoans;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Loan\Loan;

class AdminController extends BaseController
{

    protected $lenderQuery, $borrowerQuery, $countryQuery;
    protected $borrowersForm, $lendersForm, $loansForm;
    private $loanService;

    public function  __construct(
        LenderQuery $lenderQuery,
        BorrowerQuery $borrowerQuery,
        CountryQuery $countryQuery,
        FilterBorrowers $borrowersForm,
        FilterLenders $lendersForm,
        FilterLoans $loansForm,
        LoanService $loanService
    ) {
        $this->lenderQuery = $lenderQuery;
        $this->$borrowerQuery = $borrowerQuery;
        $this->countryQuery = $countryQuery;
        $this->borrowersForm = $borrowersForm;
        $this->lendersForm = $lendersForm;
        $this->loansForm = $loansForm;
        $this->loanService = $loanService;
    }

    public
    function getDashboard()
    {
        return View::make('admin.dashboard');
    }

    public function getBorrowers()
    {
        $page = Request::query('page') ?: 1;
        $countryId = Request::query('country') ?: null;
        $email = Request::query('email') ?: null;

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
        $page = Request::query('page') ?: 1;
        $countryId = Request::query('country') ?: null;
        $email = Request::query('email') ?: null;

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
        $page = Request::query('page') ?: 1;
        $countryName = Request::query('country') ?: null;
        $status = Request::query('status') ?: null;

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
}
