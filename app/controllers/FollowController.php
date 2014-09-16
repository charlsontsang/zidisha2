<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Lender\FollowService;
use Zidisha\Lender\Lender;

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
        
        if (!$borrower) {
            App::abort(404);
        }
        
        $this->followService->follow(\Auth::user()->getLender(), $borrower);
        
        return [
            'message' => Lang::get('lender.follow.flash.follow-success'),
        ];
    }

    public function postUnfollow($borrowerId)
    {
        $borrower = BorrowerQuery::create()->findOneById($borrowerId);

        if (!$borrower) {
            App::abort(404);
        }
        
        $this->followService->unfollow(\Auth::user()->getLender(), $borrower);

        return [
            'message' => Lang::get('lender.follow.flash.unfollow-success'),
        ];
    }

    public function postUpdateFollower($borrowerId)
    {
        $borrower = BorrowerQuery::create()->findOneById($borrowerId);

        $data = [];
        if (Input::has('notifyComment')) {
            $data['notifyComment'] = (boolean) Input::get('notifyComment');
        }
        if (Input::has('notifyLoanApplication')) {
            $data['notifyLoanApplication'] = (boolean) Input::get('notifyLoanApplication');
        }

        if (!$borrower || !$data) {
            App::abort(404);
        }
        
        $this->followService->updateFollower(\Auth::user()->getLender(), $borrower, $data);

        return [
            'message' => Lang::get('lender.follow.flash.update-settings'),
        ];
    }

    public function getFollowing()
    {
        /** @var Lender $lender */
        $lender = \Auth::user()->getLender();

        $followers = $this->followService->getFollowers($lender);
        
        return View::make('lender.follow.following', [
            'lender'             => $lender,
            'fundedFollowers'    => $followers['funded'],
            'followingFollowers' => $followers['following']
        ]);
    }

}
