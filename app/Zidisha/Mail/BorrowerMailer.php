<?php
namespace Zidisha\Mail;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\FeedbackMessage;
use Zidisha\Borrower\Invite;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;
use Zidisha\User\User;

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

    public function sendLoanConfirmationMail(Borrower $borrower, Loan $loan)
    {
        $subject = \Lang::get('borrower.mails.loan-confirmation.subject');
        
        $data = [
            'parameters' => [
                'borrowerName'  => $borrower->getName(),
                'loanApplicationPage' => route('loan:index', $loan->getId()), //TODO: confirm
                'loanApplicationLink' => route('borrower:loan-application'),
                'loanApplicationDeadLine' => \Setting::get('loan.deadline'),
            ],
            'to'        => $borrower->getUser()->getEmail(),
            'from'      => 'noreply@zidisha.org',
            'subject'   => $subject,
            'label'     => 'borrower.mails.loan-confirmation.body'
        ];

        $this->mailer->send('emails.borrower.loan-confirmation', $data);
    }

    public function sendApprovedConfirmationMail(Borrower $borrower)
    {
        $subject = \Lang::get('borrowerActivation.email.approved.subject', ['name' => $borrower->getName()]);
        $data = [
            'borrowerName' => $borrower->getName(),
            'to'           => $borrower->getUser()->getEmail(),
            'from'         => 'service@zidisha.org',
            'subject'      => $subject,
        ];

        $this->mailer->send('emails.borrower.activation.approved-confirmation', $data);
    }

    public function sendDeclinedConfirmationMail(Borrower $borrower)
    {
        $subject = \Lang::get('borrowerActivation.email.declined.subject', ['name' => $borrower->getName()]);
        $data = [
            'borrowerName' => $borrower->getName(),
            'to'           => $borrower->getUser()->getEmail(),
            'from'         => 'service@zidisha.org',
            'subject'      => $subject,
        ];

        $this->mailer->send('emails.borrower.activation.declined-confirmation', $data);
    }

    public function sendBorrowerCommentNotification(Borrower $borrower, Comment $comment)
    {

    }
    
    public function sendExpiredLoanMail(Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $loan->getBorrower()->getUser()->getEmail(),
                'subject'    => 'Borrower account notifications',
                'templateId' => \Setting::get('sendwithus.borrower-expired-loan-template-id'),
            ]
        );
    }

    public function sendBorrowerInvite(Borrower $lender, Invite $borrowerInvite, $subject, $message)
    {
        $email = $borrowerInvite->getEmail();
        //TODO send invite email
    }
    
    public function sendLoanFirstArrear(User $user)
    {
        
    }

    public function sendAgainRepaymentReminder(Borrower $borrower, Loan $loan, $installments)
    {
        $totalAmount = Money::create(0, $loan->getCurrencyCode());
        $paidAmount = Money::create(0, $loan->getCurrencyCode());

        /** @var Installment $installment */
        foreach ($installments as $installment) {
            $totalAmount = $totalAmount->add($installment->getAmount());
            $paidAmount = $paidAmount->add($installment->getPaidAmount());
        }

        $dueAmount = $totalAmount->subtract($paidAmount)->round(2);

        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => 'Borrower Again Repayment Instructions',
                'templateId' => \Setting::get('sendwithus.borrower-again-repayment-instruction-template-id'),
            ]
        );

    }

    public function sendRepaymentReminder(Borrower $borrower, Installment $installment)
    {
        $dueDate = $installment->getDueDate();
        $paidAmount = $installment->getPaidAmount();
        $dueAmount = $installment->getAmount()->subtract($installment->getPaidAmount());


        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower Repayment Remainder',
            ]
        );
    }

    public function sendLoanFinalArrearMail(Borrower $borrower, Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower account notifications',
            ]
        );
    }
    
    public function sendLoanFinalArrearToVolunteerMentor(VolunteerMentor $volunteerMentor, Borrower $borrower, Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower account notifications',
            ]
        );
    }

    public function sendLoanFirstArrearMail(Borrower $borrower, Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower account notifications',
            ]
        );
    }

    public function sendLoanMonthlyArrearToVolunteerMentor(VolunteerMentor $volunteerMentor, Borrower $borrower, Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower account notifications',
            ]
        );
    }

    public function sendLoanMonthlyArrearMail(Borrower $borrower, Loan $loan)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower account notifications',
            ]
        );
    }

    public function sendRepaymentReminderTommorow(Borrower $borrower, Installment $installment)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower Repayment Remainder',
            ]
        );
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Loan $loan, $amounts)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'      => $borrower->getUser()->getEmail(),
                'subject' => 'Borrower Repayment Remainder',
            ]
        );
    }

    public function sendLoanFinalArrearToInvite(Invite $invite, Borrower $borrower, Loan $loan)
    {
        //TODO: sendLoanFinalArrearToInvite
    }

    public function sendLoanFullyFundedMail($loan)
    {
        /// See $session->SendFullyFundedMail
    }
}
