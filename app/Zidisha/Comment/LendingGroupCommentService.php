<?php
namespace Zidisha\Comment;


use Zidisha\Lender\LendingGroup;
use Zidisha\Mail\AdminMailer;
use Zidisha\Mail\UserMailer;
use Zidisha\Upload\Upload;

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
     * @param array $data
     * @return LendingGroupComment
     */
    protected function createComment($data = [])
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

        $lendingGroupSubscribers = $lendingGroup->getLendingGroupNotificationsJoinUser();

        $users = [];
        foreach ($lendingGroupSubscribers as $lendingGroupSubscriber) {
            $users[] = $lendingGroupSubscriber->getUser();
        }

        $images = $this->getImages($comment);
        foreach ($users as $user) {
            if ($comment->getUser() != $user) {
                $this->userMailer->sentLendingGroupCommentNotification($lendingGroup, $comment, $user, $images);
            }
        }
    }

    protected function getImages(LendingGroupComment $comment)
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
}
