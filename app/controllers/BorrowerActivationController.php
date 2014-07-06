<?php

use Zidisha\Borrower\BorrowerActivationService;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Form\Activation\ReviewForm;

class BorrowerActivationController extends BaseController
{

    /**
     * @var Zidisha\Borrower\BorrowerActivationService
     */
    private $borrowerActivationService;

    public function __construct(BorrowerActivationService $borrowerActivationService)
    {
        $this->borrowerActivationService = $borrowerActivationService;
    }

    public function getIndex()
    {
        $page = Request::query('page') ? : 1;

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

        $reviewForm = new ReviewForm($borrower);

        if (!$borrower) {
            App::abort(404);
        }

        return View::make(
            'admin.borrower-activation.edit',
            compact('borrower', 'reviewForm')
        );
    }

    public function postReview($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        $reviewForm = new ReviewForm($borrower);
        
        if (!$reviewForm->isValid()) {
            return Redirect::route('admin:borrower-activation:edit', $borrower->getId());
        }
        
        $data = Input::only('isAddressLocatableNote');
        $data['isAddressLocatable'] = (boolean) Input::get('isAddressLocatable');
        
        $review = $this->borrowerActivationService->review($borrower, Auth::user(), $data);

        return Redirect::route('admin:borrower-activation:edit', [$borrower->getId(), '#review']);
    }
}
