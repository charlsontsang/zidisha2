<?php

use Zidisha\Admin\AdminNote;
use Zidisha\Admin\AdminNoteQuery;
use Zidisha\Admin\Form\AuthorizeLoanForm;
use Zidisha\Admin\Form\DisburseLoanForm;
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
    
    public function postPendingDisbursements()
    {
        if (!\Input::get('countryCode')) {
            \App::abort(404, 'Please select country');
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
            \App::abort(404, 'Please select country');
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

        $loans->populateRelation('AdminNote');

        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        $_adminNotes = AdminNoteQuery::create()
            ->filterByLoanId($loans->toKeyValue('id', 'id'))
            ->joinWith('User')
            ->find();
        
        $adminNotes = [];
        foreach ($_adminNotes as $loanNote) {
            if (!isset($adminNotes[$loanNote->getLoanId()])) {
                $adminNotes[$loanNote->getLoanId()] = [];
            }
            $adminNotes[$loanNote->getLoanId()][] = $loanNote;
        }

        return View::make(
            'admin.pending-disbursements.pending-disbursements',
            compact(
                'loans', 'adminNotes', 'exchangeRate',
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

        $loanNote = new AdminNote();
        $loanNote
            ->setUser($user)
            ->setLoanId($loanId)
            ->setType('disbursement')
            ->setNote($note);
        $loanNote->save();

        return \Redirect::back();
    }

    public function postAuthorize($loanId)
    {
        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan || $loan->isAuthorized() || $loan->getStatus() != Loan::FUNDED) {
            App::abort(404, 'Loan not found');
        }
        
        $form = new AuthorizeLoanForm();
        $form->handleRequest(Request::instance());
        
        if ($form->isValid()) {
            $data = $form->getData();
            $this->loanService->authorizeLoan($loan, [
                'authorizedAt'     => \DateTime::createFromFormat('m/d/Y', $data['authorizedAt']),
                'authorizedAmount' => Money::create($data['authorizedAmount'], $loan->getCurrencyCode()),
            ]);
            
            \Flash::success('Successfully authorized loan.');
        } else {
            foreach ($form->getMessageBag()->all() as $error) {
                \Flash::error($error);
            }   
        }
        
        return \Redirect::backAppend("#loan-id-$loanId");
    }

    public function postDisburse($loanId)
    {
        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan || !$loan->isAuthorized() || $loan->getStatus() != Loan::FUNDED) {
            App::abort(404, 'Loan not found');
        }

        $form = new DisburseLoanForm($loan);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->loanService->disburseLoan($loan, [
                'disbursedAt'     => \DateTime::createFromFormat('m/d/Y', $data['disbursedAt']),
                'disbursedAmount' => Money::create($data['disbursedAmount'], $loan->getCurrencyCode()),
                'registrationFee' => Money::create(array_get($data, 'registrationFee', 0), $loan->getCurrencyCode()),
            ]);

            \Flash::success('Successfully authorized loan.');
            return \Redirect::back();
        }
        
        foreach ($form->getMessageBag()->all() as $error) {
            \Flash::error($error);
        }

        return \Redirect::backAppend("#loan-id-$loanId");
    }
}
