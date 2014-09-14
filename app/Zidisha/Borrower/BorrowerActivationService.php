<?php

namespace Zidisha\Borrower;


use Zidisha\Borrower\Borrower;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\User\User;
use Zidisha\Vendor\PropelDB;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class BorrowerActivationService
{

    /**
     * @var \Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;
    /**
     * @var siftScienceService
     */
    private $siftScienceService;

    public function __construct(BorrowerMailer $borrowerMailer, siftScienceService $siftScienceService)
    {
        $this->borrowerMailer = $borrowerMailer;
        $this->siftScienceService = $siftScienceService;
    }
    
    public function review(Borrower $borrower, User $user, $data)
    {
        $review = ReviewQuery::create()
            ->filterByBorrower($borrower)
            ->findOne();
        
        if (!$review) {
            $review = new Review();
            $review
                ->setBorrower($borrower)
                ->setCreatedBy($user->getId());
        } else {
            $review->setModifiedBy($user->getId());
        }
                
        $review
            ->setIsAddressLocatable($data['isAddressLocatable'])
            ->setIsAddressLocatableNote($data['isAddressLocatableNote']);
        
        PropelDB::transaction(function() use ($review, $borrower) {
            $review->save();
            $borrower->setActivationStatus($review->isCompleted() ? Borrower::ACTIVATION_REVIEWED : Borrower::ACTIVATION_INCOMPLETE);
            $borrower->save();
        });
        
        return $review;
    }

    public function addActivationFeedback(Borrower $borrower, $data)
    {
        $feedbackMessage =  new FeedbackMessage();
        $feedbackMessage
            ->setCc($data['cc'])
            ->setReplyTo($data['replyTo'])
            ->setSubject($data['subject'])
            ->setMessage($data['message'])
            ->setBorrowerEmail($data['borrowerEmail'])
            ->setSenderName($data['senderName'])
            ->setSentAt(new \DateTime())
            ->setBorrower($borrower)
            ->setType(FeedbackMessage::ACTIVATION_TYPE);

        $feedbackMessage->save();

        $this->borrowerMailer->sendFeedbackMail($feedbackMessage);

        return $feedbackMessage;
    }

    public function getFeedbackMessages(Borrower $borrower)
    {
        return FeedbackMessageQuery::create()
            ->filterByActivationType()
            ->filterByBorrower($borrower)
            ->orderByCreatedAt('desc')
            ->find();
    }

    public function verify(Borrower $borrower, User $user, $data)
    {
        $borrower->setActivationStatus($data['isEligibleByAdmin'] ? Borrower::ACTIVATION_APPROVED : Borrower::ACTIVATION_DECLINED);
        $borrower->save();
        
        if ($borrower->isActivationApproved()) {
            $this->borrowerMailer->sendApprovedConfirmationMail($borrower);
        } else {
            $this->borrowerMailer->sendDeclinedConfirmationMail($borrower);
            $this->siftScienceService->sendBorrowerDeclinedLabel($borrower);
        }
    }
}
