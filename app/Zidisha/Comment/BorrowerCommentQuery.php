<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\BorrowerCommentQuery as BaseBorrowerCommentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'borrower_comments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class BorrowerCommentQuery extends BaseBorrowerCommentQuery
{

    public function filterByReceiverId($id)
    {
        return $this->filterByBorrowerId($id);
    }
} // BorrowerCommentQuery
