<?php
namespace Zidisha\Mail;


use Zidisha\Borrower\Borrower;

class BorrowerMailer{

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendVerificationMail(Borrower $borrower)
    {
        $data = [
            'hashCode' => $borrower->getJoinLog()->getVerificationCode(),
            'to' => $borrower->getUser()->getEmail(),
            'from' => 'service@zidisha.org',
            'subject' => 'Zidisha Borrower Account Verification'
        ];

        $this->mailer->send(
            'emails.borrower.verification',
            $data
        );
    }

    public function sendJoinConfirmationMail(Borrower $borrower)
    {
        $data = [
            'borrower' => $borrower,
            'to'        => $borrower->getUser()->getEmail(),
            'from'      => 'noreply@zidisha.org',
            'subject'   => \Lang::get('borrowerJoin.emails.subject.join-confirmation')
        ];

        $this->mailer->send('emails.borrower.join-confirmation', $data);
    }

    public function sendFormResumeLaterMail($email, $resumeCode)
    {
        $data = [
            'resumeCode' => $resumeCode,
            'to' => $email,
            'from' => 'service@zidisha.org',
            'subject' => 'Zidisha Borrower Account Verification'
        ];

        $this->mailer->send(
            'emails.borrower.resumeLater',
            $data
        );
    }

    public function sendBorrowerVolunteerMail(Borrower $borrower)
    {
        $data = [
            'borrower' => $borrower,
            'to'        => $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getUser()->getEmail(),
            'from'      => 'service@zidisha.org',
            'subject'   => \Lang::get('borrowerJoin.emails.subject.borrower-volunteer-notify',
                    array('name' => $borrower->getName()))
        ];

        $this->mailer->send('emails.borrower.volunteer-mentor-notify', $data);
    }
}