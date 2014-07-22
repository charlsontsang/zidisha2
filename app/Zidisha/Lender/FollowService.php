<?php

namespace Zidisha\Lender;


use Zidisha\Borrower\Borrower;
use Zidisha\Vendor\PropelDB;

class FollowService {

    public function follow(Lender $lender, Borrower $borrower, $data = [])
    {
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findone();
        
        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setLender($lender)
                ->setBorrower($borrower);
        }
        
        $follower
            ->setActive(true)
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
            ->findone();

        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setLender($lender)
                ->setBorrower($borrower)
                ->setNotifyComment($lender->getPreferences()->getNotifyComment())
                ->setNotifyLoanApplication($lender->getPreferences()->getNotifyLoanApplication());
        }

        $follower->setActive(false);
        $follower->save();

        return $follower;
    }

    public function updateFollower(Lender $lender, Borrower $borrower, $data = [])
    {
        $follower = FollowerQuery::create()
            ->filterByLender($lender)
            ->filterByBorrower($borrower)
            ->findone();

        if (!$follower) {
            $follower = new Follower();
            $follower
                ->setActive(true)
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
            ->filterByActive(true)
            ->count();

        $query = "SELECT COUNT(DISTINCT l.id)
                  FROM lenders l
                  JOIN (SELECT lender_id FROM loan_bids 
                         WHERE loan_id IN (SELECT loan_id FROM loans WHERE borrower_id = :borrowerId)
                           AND active = true) b
                    ON l.id = b.lender_id
                  JOIN lender_preferences p ON p.lender_id = l.id 
                  WHERE l.id NOT IN (SELECT lender_id FROM followers WHERE borrower_id = :borrowerId)
                    AND l.active = true
                    AND (p.notify_comment = true OR p.notify_comment = true)";
        $count += PropelDB::fetchNumber($query, ['borrowerId' => $borrower->getId()]);

        return $count;
    }

}
