<?php

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

    public function getIndex($stage = null, $category = null, $country = null)
    {
        // for categories
        $loanCategories = $this->loanCategoryQuery
            ->orderByRank()
            ->find();

        //for countries
        $countries = $this->countryQuery
            ->orderByName()
            ->find();

        //for loans
        $conditions = [];

        $loanCategoryName = $category;
        $stageName = $stage;
        $selectedLoanCategory = $this->loanCategoryQuery
            ->findOneBySlug($loanCategoryName);

        $routeParams = [
            'category' => 'all',
            'stage' => 'fund-raising',
            'country' => 'everywhere'
        ];

        if ($stageName == 'completed') {
            $routeParams['stage'] = 'completed';
//            $conditions['status'] = [3, 5];
        } elseif ($stageName == 'active') {
            $routeParams['stage'] = 'active';
//            $conditions['status'] = [1, 2];
        } else {
            $routeParams['stage'] = 'fund-raising';
//            $conditions['status'] = 0;
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

        return View::make(
            'pages.lend',
            compact(
                'countries', 'selectedCountry', 'loanCategories',
                'selectedLoanCategory', 'paginator', 'routeParams',
                'searchQuery', 'searchRouteParams'
            )
        );

    }
}