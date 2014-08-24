<?php

use Carbon\Carbon;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Statistic\StatisticsService;

class PageController extends BaseController {

    private $borrowerService;
    private $statisticsService;
    private $countryQuery;

    public function __construct(BorrowerService $borrowerService, StatisticsService $statisticsService, CountryQuery $countryQuery)
    {

        $this->borrowerService = $borrowerService;
        $this->statisticsService = $statisticsService;
        $this->countryQuery = $countryQuery;
    }

	public function getOurStory() {
        return View::make('pages.our-story');
    }

    public function getHowItWorks() {
        return View::make('pages.how-it-works');
    }

    public function getWhyZidisha() {
        return View::make('pages.why-zidisha');
    }

    public function getTrustAndSecurity() {
        return View::make('pages.trust-and-security');
    }

    public function getPress() {
        return View::make('pages.press');
    }

    public function getTermsOfUse(){
        return View::make('pages.terms-and-conditions');
    }

    public function getContact(){
        return View::make('pages.contact');
    }

    public function getVolunteer(){
        return View::make('pages.volunteer');
    }

    public function getDonate(){
        return View::make('pages.donate');
    }

    public function getVolunteerMentorGuidelines(){
        return View::make('borrower.volunteerMentor.guidelines');
    }

    public function getVolunteerMentorCodeOfEthics(){
        return View::make('borrower.volunteerMentor.code-of-ethics');
    }

    public function getVolunteerMentorFaq(){
        return View::make('borrower.volunteerMentor.frequently-asked-questions');
    }

    public function getFeatureCriteria()
    {
        //TODO change links on this page
        return View::make('borrower.feature-criteria');
    }

    public function getProjectUpdates()
    {
        $page = Input::get('page', 1);

        $comments = BorrowerCommentQuery::create()
            ->filterByPublished(true)
            ->orderByCreatedAt('desc')
            ->joinWith('User')
            ->paginateWithUploads($page, 10);

        return View::make('pages.project-updates', compact('comments'));
    }

    public function getFaq()
    {
        $paramArray = $this->borrowerService->getFaqParameterArray();
        return View::make('pages.faq');
    }

    public function getTeam()
    {
        return View::make('pages.team');
    }

    public function getStatistics($timePeriod = null, $country = null)
    {
        $timePeriods = array(
            'one_month_ago'    => 'Past 1 month',
            'three_months_ago' => 'Past 3 months',
            'six_months_ago'   => 'Past 6 months',
            'year_ago'         => 'Past year',
            'all_time'         => 'All time',
        );
        $start_date_values = array(
            'one_month_ago'    => Carbon::now()->subMonths(1),
            'three_months_ago' => Carbon::now()->subMonths(3),
            'six_months_ago'   => Carbon::now()->subMonths(6),
            'year_ago'         => Carbon::now()->subYear(),
            'all_time'         => null,
        );
        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(true);
        $routeParams = [
            'timePeriod' => 'year_ago',
            'country' => 'everywhere'
        ];
        $selectedTimePeriod = $timePeriod;
        if (!$timePeriod) {
            $selectedTimePeriod = 'year_ago';
        } else {
            $routeParams['timePeriod'] = $timePeriod;
        }
        $selectedStartDate = array_get($start_date_values, $selectedTimePeriod);
        $selectedCountry = $this->countryQuery->findOneBySlug($country);
        if ($country) {
            $totalStats = $this->statisticsService->getStatistics('totalStatistics', $selectedStartDate, null);
            if ($selectedCountry) {
                $routeParams['country'] = $selectedCountry->getSlug();
                $lendingStats = $this->statisticsService->getStatistics('lendingStatistics-' . $timePeriod, $selectedStartDate, $selectedCountry->getId());
            } else {
                $lendingStats = $this->statisticsService->getStatistics('lendingStatistics-' . $timePeriod, $selectedStartDate, null);
            }
        }

        if(!empty($totalStats)){
            $totalStatistics = unserialize($totalStats);
        }else{
            $totalStatistics = $this->statisticsService->getTotalStatistics();
//            $database->setStatistics('totalStatistics', serialize($totalStatistics), '');
        }

        if(!empty($lendingStats)){
            $lendingStatistics = unserialize($lendingStats);
        }else{
            if ($selectedCountry) {
                $lendingStatistics = $this->statisticsService->getLendingStatistics($selectedStartDate, $selectedCountry->getId());
            } else {
                $lendingStatistics = $this->statisticsService->getLendingStatistics($selectedStartDate, null);
            }
//            $database->setStatistics('lendingStatistics-' . $selected_start_date, serialize($lendingStatistics), $c);
        }

        return View::make('pages.statistics',
            compact('totalStatistics', 'lendingStatistics', 'time', 'countries', 'selectedCountry', 'timePeriods', 'selectedTimePeriod', 'routeParams')
        );
    }
}
