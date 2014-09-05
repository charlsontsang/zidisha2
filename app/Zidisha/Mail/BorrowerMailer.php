<?php
namespace Zidisha\Mail;


use Carbon\Carbon;
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

    public function sendRepaymentReminder(Borrower $borrower, Installment $dueInstallment)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
        ];

        $body = \Lang::get('borrower.mails.reminder.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFinalArrearMail(Borrower $borrower, Installment $dueInstallment)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'contacts'              => nl2br($borrower->getContactsList()),
            'dueAmt'                => $dueInstallment->getAmount(),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions()
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-final.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-final.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFirstArrearMail(Borrower $borrower, Installment $dueInstallment)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount(),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions()
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-first.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-first.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
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

    public function sendLoanMonthlyArrearMail(Borrower $borrower)
    {
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'contacts'     => nl2br($borrower->getContactsList()),
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-monthly.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-monthly.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReminderTomorrow(Borrower $borrower, Installment $dueInstallment)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
            'paidAmt'               => $dueInstallment->getPaidAmount(),
        ];

        $body = \Lang::get('borrower.mails.reminder-advance.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-advance.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueAmount,
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
            'pastDueAmt'            => $dueAmount->subtract($dueInstallment->getAmount()), //TODO
        ];

        $body = \Lang::get('borrower.mails.reminder-postDue.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-postDue.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFinalArrearToInvite(Invite $invite, Borrower $borrower, Loan $loan)
    {
        //TODO: sendLoanFinalArrearToInvite
    }


    public function sendDisbursedLoanMail(Loan $loan)
    {
        $borrower = $loan->getBorrower();
        $disbursedAmount = $loan->getDisbursedAmount();
        $registrationFee = $borrower->getCountry()->getRegistrationFee();
        $repaymentInstruction = '';
        $countryInstruction = $borrower->getCountry()->getRepaymentInstructions();
        if (!empty($countryInstruction)) {
            $repaymentInstruction = nl2br($countryInstruction);
        }
        $parameters = [
            'borrowerName'         => $borrower->getName(),
            'disbursedAmount'      => $disbursedAmount->getAmount(),
            'registrationFee'      => $registrationFee->getAmount(),
            'netAmount'            => $disbursedAmount->subtract($registrationFee)->getAmount(),
            'zidishaLink'          => route('home'),
            'repaymentInstruction' => $repaymentInstruction
        ];

        $data['image_src'] = $borrower->getUser()->getProfilePictureUrl();
        $body = \Lang::get('borrower.mails.loan-disbursed.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-disbursed.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFullyFundedMail(Loan $loan)
    {
        $borrower = $loan->getBorrower();
        $deadlineDays = \Setting::get('loan.deadline');
        $appliedAt = Carbon::instance($loan->getAppliedAt());
        $expireDate = $appliedAt->addDays($deadlineDays);
        $message = \Lang::get('borrower.mails.loan-fully-funded.body', ['borrowerName' => $borrower->getName(), 'loanApplicationPage' => route('loan:index', $loan->getId()), 'expiryDate' => $expireDate->toFormattedDateString()]);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $loan->getBorrower()->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-fully-funded.subject'),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }
}
