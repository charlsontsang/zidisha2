<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LenderGroupCommentQuery as BaseLenderGroupCommentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'lender_group_comment' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LenderGroupCommentQuery extends BaseLenderGroupCommentQuery
{

    public function filterByReceiverId($id)
    {
        return $this->filterByLendingGroupId($id);
    }
} // LenderGroupCommentQuery
