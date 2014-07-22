<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Lender\FollowService;
use Zidisha\Loan\LoanQuery;

class FollowController extends BaseController
{

    /**
     * @var Zidisha\Lender\FollowService
     */
    private $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function postFollow($borrowerId)
    {
        $borrower = BorrowerQuery::create()->findOneById($borrowerId);
        $loan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderByCreatedAt('desc')
            ->findOne();
        
        if (!$borrower || !$loan) {
            App::abort(404);
        }
        
        $notifyComment = (boolean) Input::get('notifyComment');
        $notifyLoanApplication = (boolean) Input::get('notifyLoanApplication');
        
        $this->followService->follow(
            \Auth::user()->getLender(),
            $borrower,
            compact('notifyComment', 'notifyLoanApplication')
        );
        
        \Flash::success('lender.follow.flash.follow-success');
        
        // TODO
        return Redirect::route('loan:index', ['loanId' => $loan->getId()]);
    }

    public function postUnfollow($borrowerId)
    {
        $borrower = BorrowerQuery::create()->findOneById($borrowerId);
        $loan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderByCreatedAt('desc')
            ->findOne();

        if (!$borrower || !$loan) {
            App::abort(404);
        }
        $this->followService->unfollow(\Auth::user()->getLender(), $borrower);

        \Flash::success('lender.follow.flash.unfollow-success');

        // TODO
        return Redirect::route('loan:index', ['loanId' => $loan->getId()]);
    }

    public function postUpdateFollower($borrowerId)
    {
        $borrower = BorrowerQuery::create()->findOneById($borrowerId);
        $loan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderByCreatedAt('desc')
            ->findOne();

        $data = [];
        if (Input::has('notifyComment')) {
            $data['notifyComment'] = (boolean) Input::get('notifyComment');
        }
        if (Input::has('notifyLoanApplication')) {
            $data['notifyLoanApplication'] = (boolean) Input::get('notifyLoanApplication');
        }

        if (!$borrower || !$loan || !$data) {
            App::abort(404);
        }
        
        $this->followService->updateFollower(\Auth::user()->getLender(), $borrower, $data);

        return Response::make();
    }

}
