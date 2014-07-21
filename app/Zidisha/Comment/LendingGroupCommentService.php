<?php
namespace Zidisha\Comment;


use Zidisha\Lender\LendingGroup;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\UserMailer;

class LendingGroupCommentService extends CommentService
{
    /**
     * @var \Zidisha\Mail\UserMailer
     */
    private $userMailer;
    /**
     * @var \Zidisha\Mail\AdminMailer
     */
    private $adminMailer;

    public function __construct(UserMailer $userMailer, AdminMailer $adminMailer)
    {
        $this->userMailer = $userMailer;
        $this->adminMailer = $adminMailer;
    }

    /**
     * @return Comment
     */
    protected function createComment()
    {
        return new LendingGroupComment();
    }

    protected function createCommentQuery()
    {
        return LendingGroupCommentQuery::create();
    }

    protected function notify(Comment $comment)
    {
        /** @var LendingGroup $lendingGroup */
        $lendingGroup = $comment->getCommentReceiver();

        $groupSubscribers = $lendingGroup->getLendingGroupNotificationsJoinUser();

        $users = [];
        foreach ($groupSubscribers as $groupSubscriber) {
            $users[] = $groupSubscriber->getUser();
        }

        foreach ($users as $user) {
            if ($comment->getUser() != $user) {
                $this->userMailer->sentLendingGroupCommentNotification($user);
            }
        }

        $this->adminMailer->sendLendingGroupCommentNotification($comment);

    }
}
