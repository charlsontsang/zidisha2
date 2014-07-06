<?php

use Zidisha\Admin\Form\ExchangeRateForm;
use Zidisha\Admin\Form\FeatureFeedbackForm;
use Zidisha\Admin\Form\FilterBorrowers;
use Zidisha\Admin\Form\FilterLenders;
use Zidisha\Admin\Form\FilterLoans;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\FeedbackMessageQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Currency\CurrencyService;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Loan\Loan;

class BorrowerActivationController extends BaseController
{

    public function getIndex()
    {
        $page = Request::query('page') ?: 1;
        
        $paginator = BorrowerQuery::create()
            ->filterByVerified(true)
            ->orderByCreatedAt() // Todo registration date
            ->joinWith('Profile')
            ->paginate($page, 50);
        
        $paginator->populateRelation('Contact');
        $paginator->populateRelation('User');
        $paginator->populateRelation('Country');

        return View::make('admin.borrower-activation.index', compact('paginator'));
    }

    public function getEdit($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        return View::make(
            'admin.borrower-activation.edit',
            compact('borrower')
        );
    }
}
