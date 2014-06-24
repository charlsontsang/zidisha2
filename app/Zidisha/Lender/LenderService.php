<?php
namespace Zidisha\Lender;

use Zidisha\Mail\LenderMailer;
use Zidisha\User\User;

class LenderService
{

    private $inviteQuery;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;

    public function __construct(InviteQuery $inviteQuery, LenderMailer $lenderMailer)
    {

        $this->inviteQuery = $inviteQuery;
        $this->lenderMailer = $lenderMailer;
    }

    public function editProfile(Lender $lender, $data)
    {
        $lender->setFirstName($data['firstName']);
        $lender->setLastName($data['lastName']);
        $lender->getUser()->setEmail($data['email']);
        $lender->getUser()->setUsername($data['username']);
        $lender->getProfile()->setAboutMe($data['aboutMe']);

        if (!empty($data['password'])) {
            $lender->getUser()->setPassword($data['password']);

        }

        $lender->save();
    }

    public function uploadPicture(Lender $lender, $image)
    {
        $user = $lender->getUser();

        if ($image) {
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);

            $user->setProfilePicture($upload);
            $user->save();
        }
    }

    public function lenderInviteViaEmail($lender, $email, $subject, $custom_message)
    {

        $lender_invite = new Invite();
        $lender_invite->setLender($lender);
        $lender_invite->setEmail($email);
        $lender_invite->isInvited(true);
        $success = $lender_invite->save();

        if ($success) {
            $this->lenderMailer->sendLenderInvite($lender, $lender_invite, $subject, $custom_message);
        }

        return $lender_invite;
    }
}
