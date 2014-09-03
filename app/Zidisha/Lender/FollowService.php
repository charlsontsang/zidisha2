<?php

namespace Zidisha\Lender;


use Zidisha\Borrower\Borrower;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Loan\Loan;
use Zidisha\Vendor\PropelDB;

class FollowService {

    public function follow(Lender $lender, Borrower $borrower, $data = [])
    {
        $data += [
            'notifyComment' => true,
            'notifyLoanApplication' => true,
        ];
        
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findOne();
        
        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setLender($lender)
                ->setBorrower($borrower);
        }
        
        $follower
            ->setNotifyComment($data['notifyComment'])
            ->setNotifyLoanApplication($data['notifyLoanApplication']);
        $follower->save();

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

}
