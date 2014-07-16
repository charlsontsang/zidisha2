<?php

use Zidisha\Country\CountryQuery;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;

class PendingDisbursementsController extends BaseController
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
            ->filterByBorrowerCountry(true)
            ->find();

        return View::make('admin.pending-disbursements.pending-disbursements-select-country', compact('countries'));
    }

    public function postPendingDisbursements()
    {
        if (!\Input::get('countryCode')) {
            \App::abort(404, 'Please select proper country');
        }

        $countryCode = \Input::get('countryCode');

        return Redirect::action(
            'PendingDisbursementsController@getPendingDisbursementsByCountry',
            ['countryCode' => $countryCode]
        );
    }

    public function getPendingDisbursementsByCountry($countryCode)
    {
        $page = Input::get('page', 1);

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
            ->orderByAcceptedAt('desc')
            ->joinWith('Borrower')
            ->joinWith('Borrower.Profile')
            ->paginate($page, 10);

        $loans->populateRelation('LoanNote');

        return View::make('admin.pending-disbursements.pending-disbursements', compact('loans', 'exchangeRate', 'currency'));
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

    public function postAuthorize()
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

    public function postDisburse()
    {
        $loanId = Input::get('loanId');
        $disbursedAt  = \DateTime::createFromFormat('m/d/Y', Input::get('disbursedAt'));

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        $principalAmount = Money::create(Input::get('principalAmount'), $loan->getCurrencyCode());

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $loan->setDisbursedAt($disbursedAt);
        $loan->save();

        $this->loanService->disburseLoan($loan, $disbursedAt, $principalAmount);

        return \Redirect::back();
    }
}