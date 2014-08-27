<?php

use Zidisha\Loan\Loan;
use Zidisha\Utility\Utility;

class LendController extends BaseController
{

    protected $loanCategoryQuery;
    protected $countryQuery;
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function  __construct(
        Zidisha\Loan\CategoryQuery $loanCategoryQuery,
        Zidisha\Country\CountryQuery $countryQuery,
        \Zidisha\Loan\LoanService $loanService
    )
    {
        $this->loanCategoryQuery = $loanCategoryQuery;
        $this->countryQuery = $countryQuery;
        $this->loanService = $loanService;
    }

    public function getIndex($stage = null, $category = 'null', $country = null)
    {
        // for categories
        $loanCategories = $this->loanCategoryQuery
            ->orderByName()
            ->find();

        //for countries
        $countries = $this->countryQuery
            ->orderByName()
            ->find();

        //for loans
        $conditions = [];
        $sortConditions = array(
            'repaymentRate'  => 'Repayment Rate',
            'recentlyAdded'  => 'Recently Added',
            'expiringSoon'   => 'Expiring Soon',
            'almostFunded'   => 'Almost Funded',
            'mostDiscussed' => 'Most Discussed',
        );

        $loanCategoryName = $category;
        $stageName = $stage;
        $sortBy = Request::query('sortBy') ? : null;
        $selectedLoanCategory = $this->loanCategoryQuery
            ->findOneBySlug($loanCategoryName);

        $routeParams = [
            'category' => 'all',
            'stage' => 'fund-raising',
            'country' => 'everywhere',
            'sortBy' => 'repaymentRate'
        ];

        if ($stageName == 'completed') {
            $routeParams['stage'] = 'completed';
            $conditions['status'] = [Loan::DEFAULTED, Loan::REPAID];
        } elseif ($stageName == 'active') {
            $routeParams['stage'] = 'active';
            $conditions['status'] = [Loan::ACTIVE, Loan::FUNDED];
        } else {
            $routeParams['stage'] = 'fund-raising';
            $conditions['status'] = Loan::OPEN;
        }

        if ($sortBy == 'recentlyAdded') {
            $routeParams['sortBy'] = 'recentlyAdded';
            $conditions['sortBy'] = 'applied_at';
            $conditions['sortByOrder'] = 'asc';
        } elseif ($sortBy == 'expiringSoon') {
            $routeParams['sortBy'] = 'expiringSoon';
            $conditions['sortBy'] = 'applied_at';
            $conditions['sortByOrder'] = 'desc';
        } else {
            //TODO sorting by repaymentRate
            $conditions['sortBy'] = 'id';
            $conditions['sortByOrder'] = 'asc';
        }

        if ($selectedLoanCategory) {
            $conditions['categoryId'] = $selectedLoanCategory->getId();
            $routeParams['category'] = $selectedLoanCategory->getSlug();
        }

        $countryName = $country;
        $selectedCountry = $this->countryQuery->findOneBySlug($countryName);

        if ($selectedCountry) {
            $conditions['countryId'] = $selectedCountry->getId();
            $routeParams['country'] = $selectedCountry->getSlug();
        }

        $searchRouteParams = $routeParams;

        $searchQuery = Request::query('search');
        if ($searchQuery) {
            $conditions['search'] = $searchQuery;
            $routeParams['search'] = $searchQuery;
        }

        $page = Request::query('page') ? : 1;
        $paginator = $this->loanService->searchLoans($conditions, $page);
        $countResults = $paginator->count();
        $countAll = $this->loanService->searchLoans()->count();

        return View::make(
            'pages.lend',
            compact(
                'countries', 'selectedCountry', 'loanCategories',
                'selectedLoanCategory', 'paginator', 'routeParams',
                'searchQuery', 'searchRouteParams', 'countResults', 'countAll',
                'sortConditions', 'sortBy'
            )
        );

    }
}