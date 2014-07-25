<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LoanFeedbackCommentQuery as BaseLoanFeedbackCommentQuery;


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

    /**
     * @param $id
     * @return CommentQuery
     */
    public function filterByReceiverId($id)
    {
        return $this->filterByLoanId($id);
    }
} // LoanFeedbackCommentQuery