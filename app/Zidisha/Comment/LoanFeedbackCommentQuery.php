<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LoanFeedbackCommentQuery as BaseLoanFeedbackCommentQuery;
use Zidisha\Comment\Map\LoanFeedbackCommentTableMap;
use Zidisha\Loan\Loan;


/**
 * Skeleton subclass for performing query and update operations on the 'loan_feedback_comments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LoanFeedbackCommentQuery extends BaseLoanFeedbackCommentQuery
{

    public function filterByReceiverId($id)
    {
        return $this->filterByLoanId($id);
    }

    public function getFeedbackRatingCounts(Loan $loan)
    {
        $counts = LoanFeedbackCommentQuery::create()
                ->filterByLoan($loan)
                ->filterByLevel(0)
                ->filterByRemoved(false)
                ->withColumn('COUNT(*)', 'count')
                ->select(array('rating', 'rating'))
                ->groupByRating()
                ->find();
        
        $countsByRating = [
            LoanFeedbackComment::POSITIVE => 0,
            LoanFeedbackComment::NEUTRAL => 0,
            LoanFeedbackComment::NEGATIVE => 0,
        ];

        $enumValues = LoanFeedbackCommentTableMap::getValueSet(LoanFeedbackCommentTableMap::COL_RATING);
        foreach ($counts as $count) {
            $rating = $enumValues[$count['rating']];
            $countsByRating[$rating] = $count['count'];
        }
        
        return $countsByRating;
    }

    public function countFeedback()
    {
        return $this
            ->filterByRemoved(false)
            ->filterByLevel(0)
            ->count();
    }
    
} // LoanFeedbackCommentQuery
