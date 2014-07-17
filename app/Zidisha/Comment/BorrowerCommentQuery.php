<?php

namespace Zidisha\Comment;

use Propel\Runtime\Connection\ConnectionInterface;
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

    public function paginateWithUploads($page = 1, $maxPerPage = 10, ConnectionInterface $con = null)
    {
        $comments = $this->paginate(1, 10);

        $idToComments = [];
        foreach ($comments as $comment) {
            $idToComments[$comment->getId()] = $comment;
            $comment->initUploadsWithUglyFixButItWorks();
        }

        $borrowerCommentUploads = BorrowerCommentUploadsQuery::create()
            ->filterByBorrowerComment($comments->getResults())
            ->joinWith('Upload')
            ->find();

        foreach ($borrowerCommentUploads as $borrowerCommentUpload) {
            /** @var BorrowerComment $comment */
            $comment = $idToComments[$borrowerCommentUpload->getCommentId()];
            $comment->addUploadWithUglyFixButItWorks($borrowerCommentUpload->getUpload());
        }

        return $comments;
    }
} // BorrowerCommentQuery
