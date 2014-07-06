<?php
namespace Zidisha\Mail;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\FeedbackMessage;
use Zidisha\Loan\Loan;

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

    public function sendBorrowerJoinedConfirmationMail(Borrower $borrower)
    {
        $data = [
            'borrower' => $borrower,
            'to'        => $borrower->getUser()->getEmail(),
            'from'      => 'noreply@zidisha.org',
            'subject'   => \Lang::get('borrowerJoin.emails.subject.confirmation')
        ];

        $this->mailer->send('emails.borrower.join.confirmation', $data);
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

    public function sendBorrowerJoinedVolunteerMentorConfirmationMail(Borrower $borrower)
    {
        $subject = \Lang::get('borrowerJoin.emails.subject.volunteer-mentor-confirmation', ['name' => $borrower->getName()]);
        $data = [
            'borrower' => $borrower,
            'to'        => $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getUser()->getEmail(),
            'from'      => 'service@zidisha.org',
            'subject'   => $subject,
        ];

        $this->mailer->send('emails.borrower.join.volunteer-mentor-confirmation', $data);
    }

    public function sendFeedbackMail(FeedbackMessage $feedbackMessage)
    {
        $data = [
            'feedback' => nl2br($feedbackMessage->getMessage()),
            'to'       => $feedbackMessage->getBorrowerEmail(),
            'from'     => $feedbackMessage->getReplyTo(),
            'subject'  => $feedbackMessage->getSubject()
        ];

        $this->mailer->send('emails.borrower.feedback', $data);

       // TODO necessary? or just use cc field
        foreach($feedbackMessage->getCcEmails() as $email) {
            $data['to'] = $email;
            $this->mailer->send('emails.borrower.feedback', $data);
        }
    }

    public function sendLoanConfirmation(Borrower $borrower, Loan $loan)
    {
        $subject = \Lang::get('emails.loan-confirmation-subject');
        $data = [
            'borrower'  => $borrower,
            'loan'      => $loan,
            'to'        => $borrower->getUser()->getEmail(),
            'from'      => 'noreply@zidisha.org',
            'subject'   => $subject,
        ];

        $this->mailer->send('emails.borrower.loan-confirmation', $data);
    }
}