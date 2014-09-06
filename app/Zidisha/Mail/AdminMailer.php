<?php

namespace Zidisha\Mail;

use Zidisha\Comment\Comment;
use Zidisha\Loan\Loan;
use Zidisha\User\UserQuery;

class AdminMailer
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendBorrowerCommentNotification(Loan $loan, Comment $comment, $postedBy, $images)
    {
        $admin = UserQuery::create()
            ->findOneById(\Setting::get('site.adminId'));
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'message'      => nl2br($comment->getMessage()),
            'postedBy'     => $postedBy,
            'images'       => $images,
        ];
        $message = \Lang::get('lender.mails.borrower-comment-notification.body', $parameters);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $admin->getEmail(),
                'subject'    => \Lang::get('lender.mails.borrower-comment-notification.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.comments-template-id'),
            ]
        );
    }
}
