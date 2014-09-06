<?php namespace Zidisha\Comment;

use Zidisha\Borrower\Borrower;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\PropelDB;

class BorrowerCommentService extends CommentService
{
    /**
     * @var \Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;
    /**
     * @var LenderMailer
     */
    private $lenderMailer;
    /**
     * @var \Zidisha\Mail\AdminMailer
     */
    private $adminMailer;

    public function __construct(BorrowerMailer $borrowerMailer, LenderMailer $lenderMailer, AdminMailer $adminMailer)
    {
        $this->borrowerMailer = $borrowerMailer;
        $this->lenderMailer = $lenderMailer;
        $this->adminMailer = $adminMailer;
    }

    /**
     * @return BorrowerComment
     */
    protected function createComment()
    {
        return new BorrowerComment();
    }

    protected function createCommentQuery()
    {
        return BorrowerCommentQuery::create();
    }

    protected function notify(Comment $comment)
    {
        /** @var Borrower $borrower */
        $borrower = $comment->getCommentReceiver();
        $loan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->orderByCreatedAt('desc')
            ->findOne();

        $sql = 'SELECT DISTINCT(l.lender_id)
                FROM loan_bids AS l
                JOIN lender_preferences AS P ON l.lender_id = P .lender_id
                WHERE l.loan_id = :loanId
                  AND P .notify_comment = TRUE';

        $results = PropelDB::fetchAll($sql, ['loanId' => $loan->getId()]);

        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result['lender_id'];
        }

        $lenders = LenderQuery::create()
            ->filterById($ids)
            ->find();

        foreach ($lenders as $lender) {
            if ($comment->getUserId() != $lender->getId()) {
                $this->lenderMailer->sendBorrowerCommentNotification($lender, $comment);
            }
        }

        if ($comment->getUserId() != $borrower->getId()) {
            $this->borrowerMailer->sendBorrowerCommentNotification($borrower, $loan, $comment);
        }

        $this->adminMailer->sendBorrowerCommentNotification($comment);
    }
}
