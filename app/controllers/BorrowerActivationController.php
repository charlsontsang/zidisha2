<?php

use Zidisha\Borrower\BorrowerActivationService;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\Form\Activation\FeedbackForm;
use Zidisha\Borrower\Form\Activation\ReviewForm;
use Zidisha\Borrower\Form\Activation\VerificationForm;

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
            ->filterPendingActivation()
            ->orderByCreatedAt() 
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

        $reviewForm = new ReviewForm($borrower);
        $feedbackForm = new FeedbackForm($borrower);
        $verificationForm = new VerificationForm($borrower);

        // TODO show feedbackForm when review completed?
        $feedbackMessages = $this->borrowerActivationService->getFeedbackMessages($borrower);
        
        return View::make(
            'admin.borrower-activation.edit',
            compact('borrower', 'reviewForm', 'feedbackForm', 'verificationForm', 'feedbackMessages')
        );
    }

    public function postReview($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower || ($borrower->getReview() && $borrower->getReview()->isCompleted())) {
            App::abort(404);
        }

        $reviewForm = new ReviewForm($borrower);
        
        if (!$reviewForm->isValid()) {
            return Redirect::route('admin:borrower-activation:edit', $borrower->getId());
        }
        
        $data = Input::only('isAddressLocatableNote');
        $data['isAddressLocatable'] = (boolean) Input::get('isAddressLocatable');
        
        $review = $this->borrowerActivationService->review($borrower, Auth::user(), $data);

        $fragment = $review->isCompleted() ? '#verification' : '#review';
        
        return Redirect::route('admin:borrower-activation:edit', [$borrower->getId(), $fragment]);
    }

    public function postFeedback($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }
        
        $feedbackForm = new FeedbackForm($borrower);
        $feedbackForm->handleRequest(Request::instance());

        if ($feedbackForm->isValid()) {
            $data = $feedbackForm->getData();

            $this->borrowerActivationService->addActivationFeedback($borrower, $data);

            \Flash::success("Email sent. Thanks!");
            return Redirect::route('admin:borrower-activation:edit', [$borrower->getId(), '#review']);
        }

        return Redirect::route('admin:borrower-activation:edit', [$borrower->getId(), '#review'])->withForm($feedbackForm);
    }

    public function postVerification($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower || ($borrower->getReview() && !$borrower->getReview()->isCompleted())) {
            App::abort(404);
        }

        $verificationForm = new VerificationForm($borrower);

        if (!$verificationForm->isValid()) {
            return Redirect::route('admin:borrower-activation:edit', [$borrower->getId(), '#verification']);
        }

        $data = [];
        $data['isEligibleByAdmin'] = (boolean) Input::get('isEligibleByAdmin');

        $this->borrowerActivationService->verify($borrower, Auth::user(), $data);

        return Redirect::route('admin:borrower-activation:edit', [$borrower->getId()]);
    }
}
