<?php

namespace Zidisha\Mail;

use Zidisha\Comment\Comment;
use Zidisha\Lender\LendingGroup;
use Zidisha\User\User;

class UserMailer
{

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sentLendingGroupCommentNotification(LendingGroup $lendingGroup, Comment $comment, User $user, $images)
    {
        $data = [
            'parameters' => [
                'groupName'  => $lendingGroup->getName(),
                'message'    => $comment->getMessage(),
                'byUserName' => $comment->getUser()->getUsername(),
                'date'       => $comment->getCreatedAt()->format('d-m-Y'),
                'groupLink'  => route('lender:group', $lendingGroup->getId()),
                'images'     => $images,
            ],
        ];

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'      => $user->getEmail(),
                'label'   => 'lender.mails.lending-group-comment-notification.body',
                'subject' => \Lang::get(
                    'lender.mails.lending-group-comment-notification.subject',
                    ['groupName' => $lendingGroup->getName()]
                ),
            ]
        );
    }
}
