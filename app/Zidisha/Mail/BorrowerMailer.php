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
        $parameters = [
            'borrowerName' => $borrower->getName()
        ];

        $body = \Lang::get('borrower.mails.registration-join.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.registration-join.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendFormResumeLaterMail($email, $resumeCode)
    {
        $data = [
            'parameters' => [
                'resumeLink' => route('borrower:resumeApplication', $resumeCode),
                'resumeCode' => $resumeCode
            ],
        ];

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $email,
                'label'      => 'borrower.mails.resume-registration.body',
                'subject'    => \Lang::get('borrower.mails.resume-registration.subject')
            ]
        );
    }

    public function sendBorrowerJoinedVolunteerMentorConfirmationMail(Borrower $borrower)
    {
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'vmName'       => $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getName(),
            'profileUrl'   => route('borrower:public-profile', $borrower->getUser()->getUsername())
        ];

        $body = \Lang::get('borrower.mails.volunteer-mentor-confirmation.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.volunteer-mentor-confirmation.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
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
                'loanApplicationPage' => route('loan:index', $loan->getId()),
                'loanApplicationLink' => route('borrower:loan-application'),
                'loanApplicationDeadLine' => \Setting::get('loan.deadline'),
            ],
        ];

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'label'      => 'borrower.mails.loan-confirmation.body',
                'subject'    => $subject
            ]
        );
    }

    public function sendApprovedConfirmationMail(Borrower $borrower)
    {
        $data = [
            'parameters' => [
                'borrowerName' => $borrower->getName(),
                'zidishaLink' => route('home'),
            ],
        ];

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'label'      => 'borrower.mails.approved-confirmation.body',
                'subject'    => \Lang::get('borrower.mails.approved-confirmation.subject'),
            ]
        );
    }

    public function sendDeclinedConfirmationMail(Borrower $borrower)
    {
        $parameters = [
            'borrowerName' => $borrower->getName(),
        ];

        $body = \Lang::get('borrower.mails.declined-confirmation.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.declined-confirmation.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerCommentNotification(Borrower $borrower, Comment $comment)
    {

    }
    
    public function sendExpiredLoanMail(Borrower $borrower)
    {
        $parameters = [
            'borrowerName'        => $borrower->getName(),
            'loanApplicationLink' => route('borrower:loan-application'),
            'tips'                => implode('<br>', array_slice(\Lang::get('borrower.loan-application.instructions'), 2, 4)),
        ];

        $body = \Lang::get('borrower.mails.loan-expired.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-expired.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerInvite(Borrower $borrower, Invite $borrowerInvite, $subject, $message)
    {
        $parameters = [
            'borrowLink' => route('home'),
        ];

        $link = \Lang::get('borrower.mails.invite.link', $parameters);
        $data['content'] = $message."<br/><br/>".$link;;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrowerInvite->getEmail(),
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.borrower-invite-template-id')
            ]
        );
    }
    
    public function sendAgainRepaymentReminder(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueAmount,
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
        ];

        $body = \Lang::get('borrower.mails.reminder-again.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-again.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
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
