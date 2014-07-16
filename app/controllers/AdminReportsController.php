<?php

use Zidisha\Country\CountryQuery;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;

class AdminReportsController extends BaseController
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function getPendingDisbursements()
    {
        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(1)
            ->find();

        return View::make('admin.reports.pending-disbursements-select-country', compact('countries'));
    }

    public function postPendingDisbursements()
    {
        if (!\Input::get('country')) {
            \App::abort(404, 'please select proper country');
        }

        $countryCode = \Input::get('country');

        return Redirect::action(
            'AdminReportsController@getPendingDisbursementsByCountry',
            ['countryCode' => $countryCode]
        );
    }

    public function getPendingDisbursementsByCountry($countryCode)
    {
        $page = 1;

        if (\Input::has('page')) {
            $page = Input::get('page');
        }


        $country = CountryQuery::create()
            ->findOneByCountryCode(strtoupper($countryCode));

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($country->getCurrency());

        $currency = $country->getCurrency();

        if (!$country || !$country->isBorrowerCountry()) {
            \App::abort(404, 'please select proper country');
        }

        $loans = \Zidisha\Loan\LoanQuery::create()
            ->useBorrowerQuery()
            ->filterByCountry($country)
            ->endUse()
            ->filterByStatus(Loan::FUNDED)
            ->joinWith('Borrower')
            ->joinWith('Borrower.Profile')
            ->orderBy('accepted_date', 'desc')
            ->paginate($page, 10);

        $loans->populateRelation('LoanNote');

        return View::make('admin.reports.pending-disbursements', compact('loans', 'exchangeRate', 'currency'));
    }

    public function postLoanNote()
    {
        $loanId = Input::get('loanId');
        $note = Input::get('note');

        $user = \Auth::user();

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $loanNote = new \Zidisha\Loan\LoanNote();
        $loanNote
            ->setUser($user)
            ->setLoanId($loanId)
            ->setType('disbursement')
            ->setNote($note);
        $loanNote->save();

        return \Redirect::back();
    }

    public function postAuthorizedDate()
    {
        $loanId = Input::get('loanId');
        $authorizedAt = \DateTime::createFromFormat('m/d/Y', Input::get('authorizedAt'));

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $loan->setAuthorizedAt($authorizedAt);
        $loan->save();

        return \Redirect::back();
    }

    public function postDisbursedDate()
    {
        $loanId = Input::get('loanId');
        $date  = \DateTime::createFromFormat('m/d/Y', Input::get('disbursedDate'));


        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        $principalAmount = Money::create(Input::get('principalAmount'), $loan->getCurrencyCode());

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $loan->setDisbursedDate($date);
        $loan->save();

        $this->loanService->disburseLoan($loan, $date, $principalAmount);

        return \Redirect::back();
    }
}
