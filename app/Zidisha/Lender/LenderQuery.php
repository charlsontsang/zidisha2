<?php

namespace Zidisha\Lender;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Lender\Base\LenderQuery as BaseLenderQuery;
use Zidisha\Loan\Loan;
use Zidisha\Vendor\PropelDB;


/**
 * Skeleton subclass for performing query and update operations on the 'lenders' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LenderQuery extends BaseLenderQuery
{
    public function getInactiveLendersForLoan(Loan $loan)
    {
        $query = 'SELECT id FROM lenders as l WHERE id IN ( SELECT lender_id FROM loan_bids WHERE loan_id = :loanId AND active = TRUE )  AND id NOT IN ( SELECT lender_id FROM forgiveness_loan_shares WHERE loan_id = :loanId ) AND l.active  = FALSE ';

        $inactiveLenderIds =  PropelDB::fetchAll(
            $query,
            [
                'loanId' => $loan->getId(),
            ]
        );
        return $this
            ->filterById($inactiveLenderIds);
    }

    public function getLendersForForgive(Loan $loan)
    {
        $query = 'SELECT distinct(id) FROM lenders as l, loan_bids as lb WHERE lb.loan_id = :loanId AND lb.active = TRUE l.id = lb.lender_id AND id NOT IN ( SELECT lender_id FROM forgiveness_loan_shares WHERE loan_id = :loanId )';

        $lenderIds =  PropelDB::fetchAll(
            $query,
            [
                'loanId' => $loan->getId(),
            ]
        );
        return $this
            ->filterById($lenderIds);
    }

    public function getLendersForNewLoanNotificationMail(Loan $loan)
    {
        $lenders = LenderQuery::create()
            ->distinct()
            ->joinBid()
            ->usePreferencesQuery()
                ->filterByNotifyLoanApplication(true)
            ->endUse()
            ->where('Bid.loan_id = ?', $loan->getId())
            ->filterByActive(true)
            ->find();

        $followers = LenderQuery::create()
            ->joinFollower()
            ->where('Follower.borrower_id = ?', $loan->getBorrowerId())
            ->where('Follower.notify_loan_application = true')
            ->filterById($lenders->toKeyValue('id', 'id'), Criteria::NOT_IN)
            ->filterByActive(true)
            ->find();
        
        return compact('lenders', 'followers');
    }
    
} // LenderQuery
