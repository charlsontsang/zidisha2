<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\LendingGroupCommentQuery as BaseLendingGroupCommentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'lending_group_comments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LendingGroupCommentQuery extends BaseLendingGroupCommentQuery
{

    /**
     * @param $id
     * @return CommentQuery
     */
    public function filterByReceiverId($id)
    {
        return $this->filterByLendingGroupId($id);
    }
} // LendingGroupCommentQuery
