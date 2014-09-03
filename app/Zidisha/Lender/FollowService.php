<?php

namespace Zidisha\Lender;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;
use Zidisha\Vendor\PropelDB;

class FollowService {

    /**
     * @var \Zidisha\Loan\LoanService
     */
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function follow(Lender $lender, Borrower $borrower)
    {
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findOne();
        
        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setLender($lender)
                ->setBorrower($borrower)
                ->setNotifyComment($lender->getPreferences()->getNotifyComment())
                ->setNotifyLoanApplication($lender->getPreferences()->getNotifyLoanApplication());
        }

        return $follower;
    }

    public function unfollow(Lender $lender, Borrower $borrower)
    {
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findOne();

        if ($follower) {
            $follower->delete();
        }

        return true;
    }

    public function updateFollower(Lender $lender, Borrower $borrower, $data = [])
    {
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findOne();

        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setLender($lender)
                ->setBorrower($borrower)
                ->setNotifyComment($lender->getPreferences()->getNotifyComment())
                ->setNotifyLoanApplication($lender->getPreferences()->getNotifyLoanApplication());
        }

        if (isset($data['notifyComment'])) {
            $follower->setNotifyComment($data['notifyComment']);  
        }
        if (isset($data['notifyLoanApplication'])) {
            $follower->setNotifyLoanApplication($data['notifyLoanApplication']);
        }

        $follower->save();

        return $follower;
    }
    
    public function getFollowerCount(Borrower $borrower)
    {
        $count = FollowerQuery::create()
            ->filterByBorrower($borrower)
            ->count();
        
        $lastLoanId  = LoanQuery::create()
            ->select('id')
            ->filterByBorrower($borrower)
            ->filterByStatus([Loan::ACTIVE, Loan::REPAID, Loan::DEFAULTED])
            ->orderById('desc')
            ->findOne();

        if ($lastLoanId) {
            $query = "SELECT COUNT(DISTINCT l.id)
                  FROM (SELECT DISTINCT(lender_id) FROM loan_bids 
                         WHERE loan_id = :loanId
                           AND active = true) b
                  JOIN lenders l ON l.id = b.lender_id
                  JOIN lender_preferences p ON p.lender_id = l.id 
                  WHERE l.id NOT IN (SELECT lender_id FROM followers WHERE borrower_id = :borrowerId)
                    AND l.active = true
                    AND (p.notify_comment = true OR p.notify_comment = true)";
            $count += PropelDB::fetchNumber($query, [
                'loanId' => $lastLoanId,
                'borrowerId' => $borrower->getId()
            ]);
        }

        return $count;
    }

    public function getFollowers(Lender $lender)
    {
        // TODO only for the borrower's last loan
        $query = "SELECT lb.borrower_id
                  FROM (SELECT DISTINCT(borrower_id) FROM loan_bids 
                         WHERE lender_id = :lenderId
                           AND active = true) lb
                  JOIN borrowers b ON lb.borrower_id = b.id
                  WHERE b.active = TRUE";
        $rows = PropelDB::fetchAll($query, ['lenderId' => $lender->getId()]);
        
        $fundedBorrowerIds = [];
        foreach ($rows as $row) {
            $fundedBorrowerIds[$row['borrower_id']] = $row['borrower_id'];
        }

        $followers = FollowerQuery::create()
            ->filterByLender($lender)
            ->useBorrowerQuery()
            ->filterByActive(true)
            ->endUse()
            ->find();
        
        $followersByBorrowerId = [];
        foreach ($followers as $follower) {
            $followersByBorrowerId[$follower->getBorrowerId()] = $follower;
        }

        $fundedBorrowers = BorrowerQuery::create()
            ->filterById($fundedBorrowerIds)
            ->joinWith('Country')
            ->joinWith('ActiveLoan')
            ->joinWith('User')
            ->orderByFirstName()
            ->find();

        $followingBorrowers = BorrowerQuery::create()
            ->filterById(array_diff($followers->toKeyValue('borrowerId', 'borrowerId'), $fundedBorrowerIds))
            ->joinWith('Country')
            ->joinWith('ActiveLoan')
            ->joinWith('User')
            ->orderByFirstName()
            ->find();
        
        $funded = [];
        foreach ($fundedBorrowers as $borrower) {
            if (isset($followersByBorrowerId[$borrower->getId()])) {
                /** @var Follower $follower */
                $follower = $followersByBorrowerId[$borrower->getId()];
            } else {
                $follower = new Follower();
                $follower
                    ->setNotifyComment($lender->getPreferences()->getNotifyComment())
                    ->setNotifyLoanApplication($lender->getPreferences()->getNotifyLoanApplication());
            }
            $follower
                ->setBorrower($borrower)
                ->setIsFunded();
            $funded[] = $follower;
        }

        $following = [];
        foreach ($followingBorrowers as $borrower) {
            /** @var Follower $follower */
            $follower = $followersByBorrowerId[$borrower->getId()];
            $follower->setBorrower($borrower);
            $following[] = $follower;
        }
        
        return compact('funded', 'following');
    }

    public function getFollower(Lender $lender, Borrower $borrower)
    {
        $hasFundedBorrower = $this->loanService->hasFunded($lender, $borrower);

        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findOne();

        if ($hasFundedBorrower) {
            if (!$follower) {
                $follower = new Follower();
                $follower
                    ->setNotifyComment($lender->getPreferences()->getNotifyComment())
                    ->setNotifyLoanApplication($lender->getPreferences()->getNotifyLoanApplication());
            }
            $follower->setIsFunded();
        }
    
        return $follower;
    }

}
