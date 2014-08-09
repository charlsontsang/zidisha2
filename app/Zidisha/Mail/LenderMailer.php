<?php
namespace Zidisha\Mail;


use Carbon\Carbon;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCard;
use Zidisha\Lender\Invite;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Loan\LenderRefund;
use Zidisha\Loan\Loan;
use Zidisha\Loan\RefundLender;
use Zidisha\User\User;

/**
 * Class LenderMailer
 * @package Zidisha\Mail
 */
class LenderMailer
{

    /**
     * @var mailer
     */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Bid $bid
     */
    public function sendFirstBidConfirmationMail(Bid $bid)
    {
        $email = $bid->getLender()->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.loan.first-bid-confirmation',
            [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Congratulations you have made your first Bid on Zidisha.'
            ]
        );
    }

    public function sendOutbidMail($changedBid)
    {
        /** @var Bid $bid*/
        $bid = $changedBid['bid'];
        /** @var Money $acceptedAmount */
        $acceptedAmount = $changedBid['acceptedAmount'];
        /** @var Money $changedAmount */
        $changedAmount = $changedBid['changedAmount'];
        $email = $bid->getLender()->getUser()->getEmail();

        $this->mailer->send(
            $acceptedAmount->isZero() ? 'emails.lender.loan.fully-outbid' : 'emails.lender.loan.partially-outbid',
            [
                'to'              => $email,
                'from'            => 'service@zidisha.com',
                'subject'         => 'Outbid Notification.',
                'bidAmount'       => $bid->getBidAmount()->round(2)->getAmount(),
                'bidInterestRate' => $bid->getInterestRate(),
                'outbidAmount'    => $changedAmount->round(2)->getAmount(),
                'acceptedAmount'  => $acceptedAmount->round(2)->getAmount(),
                'borrowerLink'    => $bid->getLoan()->getBorrower()->getUser()->getProfileUrl(),
                'borrowerName'    => $bid->getLoan()->getBorrower()->getName(),
            ]
        );
    }

    /**
     * @param Bid $bid
     */
    public function sendLoanFullyFundedMail(Bid $bid)
    {
        $email = $bid->getLender()->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.loan.loan-fully-funded',
            [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'The loan is fully funded.'
            ]
        );
    }

    public function sendLenderInvite(Lender $lender, Invite $lender_invite, $subject, $custom_message)
    {
        $email = $lender_invite->getEmail();
        //TODO send invite email
    }

    public function sendLenderInviteCredit(Invite $invite)
    {
        //TODO
    }

    public function sendWelcomeMail(Lender $lender)
    {
        $email = $lender->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.welcome',
            [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Welcome to Zidisha!'
            ]
        );
    }

    public function sendIntroductionMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Welcome to Zidisha!',
                'templateId' => \Setting::get('sendwithus.introduction-template-id'),
            ]
        );
    }

    public function sendGiftCardMailToSender(GiftCard $giftCard)
    {
        $email = $giftCard->getLender()->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.gift-card-sender',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
            ]
        );
    }

    public function sendGiftCardMailToRecipient(GiftCard $giftCard)
    {
        $email = $giftCard->getRecipientEmail();

        $this->mailer->send(
            'emails.lender.gift-card-receiver',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
            ]
        );
    }

    public function sendAbandonedMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.lender.abandoned',
            [
                'to'      => $lender->getUser()->getEmail(),
                'from'    => 'service@zidisha.com',
                'subject' => 'Login to Zidisha'
            ]
        );
    }

    public function sendBorrowerCommentNotification(Lender $lender, Comment $comment)
    {

    }

    public function sendLoanDefaultedMail(Loan $loan, Lender $lender)
    {
        $this->mailer->send(
            'emails.lender.loan.loan-defaulted',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Loan defaulted',
            ]
        );
    }
    
    public function sendExpiredLoanMail(Loan $loan, LenderRefund $refundLender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $refundLender->getLender()->getUser()->getEmail(),
                'subject'    => 'Loan expired notification',
                'templateId' => \Setting::get('sendwithus.lender-expired-loan-template-id'),
            ]
        );        
    }

    public function sendAbandonedUserMail(User $user)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $user->getEmail(),
                'subject'    => 'Abandoned User Notification',
            ]
        );
    }

    public function sendUnusedFundsNotification(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Unused Funds Notification',
                'templateId' => \Setting::get('sendwithus.lender-unused-funds-template-id'),
            ]
        );
    }

    public function sendLoanAboutToExpireMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Loan About to Expire Notification',
                'templateId' => \Setting::get('sendwithus.loan-about-to-expire-mail-template-id'),
            ]
        );                
    }
}
