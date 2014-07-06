<?php

namespace Zidisha\Borrower;


use Zidisha\Borrower\Borrower;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\User\User;
use Zidisha\Vendor\PropelDB;

class BorrowerActivationService
{

    /**
     * @var \Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;

    public function __construct(BorrowerMailer $borrowerMailer)
    {
        $this->borrowerMailer = $borrowerMailer;
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
            if ($review->isCompleted()) {
//                $borrower->setActivationStatus('pending-verification'); TODO
            }
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
    }
}
