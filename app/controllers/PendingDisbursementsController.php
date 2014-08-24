<?php

use Zidisha\Country\CountryQuery;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanNoteQuery;
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
    
    public function postPendingDisbursements()
    {
        if (!\Input::get('countryCode')) {
            \App::abort(404, 'Please select proper country');
        }

        $countryCode = \Input::get('countryCode');

        return Redirect::action(
            'PendingDisbursementsController@getPendingDisbursements',
            ['countryCode' => $countryCode]
        );
    }

    public function getPendingDisbursements($countryCode = 'KE')
    {
        $page = Input::get('page', 1);
        $orderBy = Input::get('orderBy', 'acceptedAt');
        $orderDirection = Input::get('orderDirection', 'asc');

        $country = CountryQuery::create()
            ->findOneByCountryCode(strtoupper($countryCode));

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($country->getCurrency());

        $currency = $country->getCurrency();

        if (!$country || !$country->isBorrowerCountry()) {
            \App::abort(404, 'please select proper country');
        }

        $loansQuery = \Zidisha\Loan\LoanQuery::create()
            ->joinWith('Borrower')
            ->joinWith('Borrower.Profile')
            ->useBorrowerQuery()
                ->filterByCountry($country)
            ->endUse()
            ->filterByStatus(Loan::FUNDED);
        
        if ($orderBy == 'borrowerName') {
            $loansQuery->orderBy('Borrower.FirstName', $orderDirection);
        } else {
           $loansQuery->orderByAcceptedAt($orderDirection);
        }
        
        $loans = $loansQuery
            ->paginate($page, 10);

        $loans->populateRelation('LoanNote');

        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        $_loanNotes = LoanNoteQuery::create()
            ->filterByLoanId($loans->toKeyValue('id', 'id'))
            ->joinWith('User')
            ->find();
        
        $loanNotes = [];
        foreach ($_loanNotes as $loanNote) {
            if (!isset($loanNotes[$loanNote->getLoanId()])) {
                $loanNotes[$loanNote->getLoanId()] = [];
            }
            $loanNotes[$loanNote->getLoanId()][] = $loanNote;
        }

        return View::make(
            'admin.pending-disbursements.pending-disbursements',
            compact(
                'loans', 'loanNotes', 'exchangeRate',
                'currency', 'country', 'countries',
                'orderBy', 'orderDirection'
            )
        );
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

        $authorizedAmount = Money::create(Input::get('authorizedAmount'), $loan->getCurrencyCode());

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $this->loanService->authorizeLoan($loan, compact('authorizedAt', 'authorizedAmount'));

        return \Redirect::back();
    }

    public function postDisburse()
    {
        $loanId = Input::get('loanId');
        $disbursedAt  = \DateTime::createFromFormat('m/d/Y', Input::get('disbursedAt'));

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        $disbursedAmount = Money::create(Input::get('disbursedAmount'), $loan->getCurrencyCode());

        if (!$loan) {
            App::abort(404, 'Loan not found');
        }

        $this->loanService->disburseLoan($loan, compact('disbursedAt', 'disbursedAmount'));

        return \Redirect::back();
    }
}
