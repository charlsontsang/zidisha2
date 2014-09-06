<?php namespace Zidisha\Comment;

use Zidisha\Borrower\Borrower;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Upload\Upload;
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

        $postedBy = $this->getPostedBy($borrower, $comment);
        $images = $this->getImages($comment);

        foreach ($lenders as $lender) {
            if ($comment->getUserId() != $lender->getId()) {
                $this->lenderMailer->sendBorrowerCommentNotification($lender, $comment);
            }
        }

        if ($comment->getUserId() != $borrower->getId()) {
            $this->borrowerMailer->sendBorrowerCommentNotification($borrower, $loan, $comment, $postedBy, $images);
        }

        $this->adminMailer->sendBorrowerCommentNotification($comment);
    }

    protected function getPostedBy(Borrower $borrower, Comment $comment)
    {
        $commenter = $comment->getUser();
        $bname_format=ucwords(strtolower($borrower->getName()));
        $sender_name=ucwords(strtolower($commenter->getUsername()));
        if ($commenter->isBorrower()) {
            $city = $commenter->getBorrower()->getProfile()->getCity();
            $country = $commenter->getBorrower()->getCountry()->getName();
            if ($city) {
                $location = ucwords(strtolower($city.", ".$country));
            } else {
                $location =  ucwords(strtolower($country));
            }
        } elseif ($commenter->isLender()) {
            $city = $commenter->getLender()->getProfile()->getCity();
            $country = $commenter->getLender()->getCountry()->getName();
            if ($city) {
                $location = ucwords(strtolower($city.", ".$country));
            } else {
                $location =  ucwords(strtolower($country));
            }
        } else {
            $location = '';
        }
        if ($comment->getUserId() != $borrower->getId()){
            $postedBy = "Posted at the profile of ".$bname_format." by ".$sender_name." in ".$location;
        } else {
            $postedBy = "Posted by ".$bname_format." in ".$location;
        }
        return $postedBy;
    }

    protected function getImages(Comment $comment)
    {
        $uploads = CommentUploadQuery::create()
            ->filterByComment($comment)
            ->find();
        $images = '';
        /** @var Upload $upload */
        foreach ($uploads as $upload) {
            if ($upload->isImage()) {
                $images .= "<br><br><a target='_blank' href='route('home')'><img src='$upload->getImageUrl('small-profile-picture')' width='100' style='border:none'></a><br>";
            }
        }
        return $images;
    }
}
