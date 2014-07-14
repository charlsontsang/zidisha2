<?php

namespace Zidisha\Comment;

use Zidisha\Comment\Base\CommentQuery as BaseCommentQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'comments' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
abstract class CommentQuery extends BaseCommentQuery
{
    /**
     * @param $id
     * @return CommentQuery
     */
    public abstract function filterByReceiverId($id);
} // CommentQuery
