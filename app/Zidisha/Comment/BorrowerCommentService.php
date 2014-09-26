<?php namespace Zidisha\Comment;

use Propel\Runtime\Propel;
use Zidisha\Borrower\Borrower;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Base\LoanQuery;
use Zidisha\Loan\Loan;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Sms\BorrowerSmsService;
use Zidisha\Upload\Upload;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\PropelDB;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class BorrowerCommentService extends CommentService
{
    private $borrowerMailer;
    private $lenderMailer;
    private $adminMailer;
    private $borrowerSmsService;
    private $siftScienceService;

    public function __construct(BorrowerMailer $borrowerMailer, LenderMailer $lenderMailer,
        AdminMailer $adminMailer, siftScienceService $siftScienceService,
        BorrowerSmsService $borrowerSmsService)
    {
        $this->borrowerMailer = $borrowerMailer;
        $this->lenderMailer = $lenderMailer;
        $this->adminMailer = $adminMailer;
        $this->borrowerSmsService = $borrowerSmsService;
        $this->siftScienceService = $siftScienceService;
    }

    /**
     * @param array $data
     * @return BorrowerComment
     */
    protected function createComment($data = [])
    {
        $comment = new BorrowerComment();
        
        if (array_get($data, 'rescheduleId')) {
            $comment->setRescheduleId(array_get($data, 'rescheduleId'));
        }
        
        return $comment;
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
                $this->lenderMailer->sendBorrowerCommentNotification($lender, $loan, $comment, $postedBy, $images);
            }
        }

        if ($comment->getUserId() != $borrower->getId()) {
            $this->borrowerMailer->sendBorrowerCommentNotification($borrower, $loan, $comment, $postedBy, $images);
            $this->borrowerSmsService->sendBorrowerCommentNotificationSms($borrower, $comment, $postedBy);
        }

        $this->siftScienceService->sendBorrowerCommentEvent($comment);
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

    protected function getImages(BorrowerComment $comment)
    {
        $uploads = $comment->getUploads();
        $images = '';
        
        /** @var Upload $upload */
        foreach ($uploads as $upload) {
            if ($upload->isImage()) {
                $images .= sprintf('<br><br><a target="_blank" href="%s"><img src="%s" width="100" style="border:none"></a><br>', route('home'), $upload->getImageUrl('small-profile-picture'));
            }
        }
        
        return $images;
    }

    public function getAllCommentForLender(Lender $lender)
    {
        $bidsQuery = "SELECT DISTINCT(l.borrower_id) FROM loans as l JOIN loan_bids as lb ON l.id = lb.loan_id WHERE lb.lender_id = :lenderId AND l.status IN (:openStatus, :fundedStatus, :activeStatus) AND l.deleted_by_admin = FALSE ";
        $bids = PropelDB::fetchAll(
            $bidsQuery,
            [
                'lenderId'     => $lender->getId(),
                'openStatus'   => Loan::OPEN,
                'fundedStatus' => Loan::FUNDED,
                'activeStatus' => Loan::ACTIVE,
            ]
        );
        $ids = array();
        if (!empty($bids)) {
            foreach ($bids as $bid) {
                array_push($ids, $bid['borrower_id']);
            }
        }
        $comments = BorrowerCommentQuery::create()
            ->filterByBorrowerId($ids)
            ->find();
        return $comments;
    }
}
