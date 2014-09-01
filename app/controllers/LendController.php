<?php

use Zidisha\Loan\Loan;

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

    public function getIndex($category = 'null', $country = null)
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
        $sortBy = Request::query('sortBy') ? : null;
        $selectedLoanCategory = $this->loanCategoryQuery
            ->findOneBySlug($loanCategoryName);

        $routeParams = [
            'category' => 'all',
            'country' => 'everywhere',
            'sortBy' => 'repaymentRate'
        ];


        $conditions['status'] = Loan::OPEN;

        if ($sortBy == 'recentlyAdded') {
            $routeParams['sortBy'] = 'recentlyAdded';
            $conditions['sortBy'] = 'applied_at';
            $conditions['sortByOrder'] = 'asc';
        } elseif ($sortBy == 'expiringSoon') {
            $routeParams['sortBy'] = 'expiringSoon';
            $conditions['sortBy'] = 'applied_at';
            $conditions['sortByOrder'] = 'desc';
        } elseif ($sortBy == 'almostFunded') {
            $routeParams['sortBy'] = 'almostFunded';
            $conditions['sortBy'] = 'raised_percentage';
            $conditions['sortByOrder'] = 'desc';
        } elseif ($sortBy == 'mostDiscussed') {
            $routeParams['sortBy'] = 'mostDiscussed';
            //TODO sorting by mostDiscussed
            $conditions['sortBy'] = 'id';
            $conditions['sortByOrder'] = 'asc';
        } else {
            $routeParams['sortBy'] = 'repaymentRate';
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
        
        $viewAllRouteParams = ['category' => 'all', 'country' => 'everywhere'] + $routeParams;
        unset($viewAllRouteParams['search']);

        $page = Request::query('page') ? : 1;
        $paginator = $this->loanService->searchLoans($conditions, $page);
        $countResults = $paginator->getTotal();
        $countAll = $this->loanService->countLoans(['status' => Loan::OPEN]);

        return View::make(
            'pages.lend',
            compact(
                'countries', 'selectedCountry', 'loanCategories',
                'selectedLoanCategory', 'paginator', 'routeParams', 'viewAllRouteParams',
                'searchQuery', 'searchRouteParams', 'countResults', 'countAll',
                'sortConditions', 'sortBy'
            )
        );

    }
}