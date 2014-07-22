<?php

namespace Zidisha\Lender;


use Zidisha\Borrower\Borrower;
use Zidisha\Vendor\PropelDB;

class FollowService {

    function getFollowerCount(Borrower $borrower)
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
