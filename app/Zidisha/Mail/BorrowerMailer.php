<?php
namespace Zidisha\Mail;


use Carbon\Carbon;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\FeedbackMessage;
use Zidisha\Borrower\Invite;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Comment\BorrowerCommentUploads;
use Zidisha\Comment\BorrowerCommentUploadsQuery;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;
use Zidisha\Upload\Upload;
use Zidisha\User\User;

class BorrowerMailer{

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendVerificationMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $hashCode = $borrower->getJoinLog()->getVerificationCode();
        $parameters = [
            'verifyLink' => route('home') . 'ident=' . $borrower->getId() . '&activate=' . $hashCode, //TODO check
        ];

        $body = \Lang::get('borrower.mails.email-verification.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.email-verification.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerJoinedConfirmationMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName()
        ];

        $body = \Lang::get('borrower.mails.registration-join.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.registration-join.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendFormResumeLaterMail($email, $resumeCode)
    {
        $parameters = [
            'resumeLink' => route('borrower:resumeApplication', $resumeCode),
            'resumeCode' => $resumeCode
        ];

        $this->mailer->queue(
            'emails.hero',
            [
                'to'         => $email,
                'content'    => \Lang::get('borrower.mails.resume-registration.body', $parameters, 'en'),
                'subject'    => \Lang::get('borrower.mails.resume-registration.subject', [], 'en'),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerJoinedVolunteerMentorConfirmationMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'vmName'       => $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getName(),
            'profileUrl'   => route('loan:index', $borrower->getLastLoanId())
        ];

        $body = \Lang::get('borrower.mails.volunteer-mentor-confirmation.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.volunteer-mentor-confirmation.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendFeedbackMail(FeedbackMessage $feedbackMessage)
    {
        $data = [
            'content'    => nl2br($feedbackMessage->getMessage()),
            'to'         => $feedbackMessage->getBorrowerEmail(),
            'from'       => $feedbackMessage->getReplyTo(),
            'subject'    => $feedbackMessage->getSubject(),
            'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
        ];

        $this->mailer->queue('emails.hero', $data);

       // TODO necessary? or just use cc field
        foreach($feedbackMessage->getCcEmails() as $email) {
            $data['to'] = $email;
            $this->mailer->queue('emails.hero', $data);
        }
    }

    public function sendLoanFeedbackMail(FeedbackMessage $feedbackMessage)
    {
        $data = [
            'content'    => nl2br($feedbackMessage->getMessage()),
            'to'         => $feedbackMessage->getBorrowerEmail(),
            'from'       => $feedbackMessage->getReplyTo(),
            'subject'    => $feedbackMessage->getSubject(),
            'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
        ];

        $this->mailer->queue('emails.hero', $data);

        // TODO necessary? or just use cc field
        foreach($feedbackMessage->getCcEmails() as $email) {
            $data['to'] = $email;
            $this->mailer->queue('emails.hero', $data);
        }
    }

    public function sendLoanConfirmationMail(Borrower $borrower, Loan $loan)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $subject = \Lang::get('borrower.mails.loan-confirmation.subject', [], $local);
        
            $parameters = [
                'borrowerName'            => $borrower->getName(),
                'loanApplicationPage'     => route('loan:index', $loan->getId()),
                'loanApplicationLink'     => route('borrower:loan-application'),
                'loanApplicationDeadLine' => \Setting::get('loan.deadline'),
            ];

        $this->mailer->queue(
            'emails.hero',
            [
                'to'         => $borrower->getUser()->getEmail(),
                'content'    => \Lang::get('borrower.mails.loan-confirmation.body', $parameters, $local),
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendApprovedConfirmationMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
            $parameters = [
                'borrowerName' => $borrower->getName(),
                'zidishaLink'  => route('home'),
            ];

        $this->mailer->queue(
            'emails.hero',
            [
                'to'         => $borrower->getUser()->getEmail(),
                'content'    => \Lang::get('borrower.mails.approved-confirmation.body', $parameters, $local),
                'subject'    => \Lang::get('borrower.mails.approved-confirmation.subject', [], $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendDeclinedConfirmationMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
        ];

        $body = \Lang::get('borrower.mails.declined-confirmation.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.declined-confirmation.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerCommentNotification(Borrower $borrower, Loan $loan, Comment $comment, $postedBy, $images)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'message'      => nl2br($comment->getMessage()),
            'postedBy'     => $postedBy,
            'images'       => $images,
        ];

        $body = \Lang::get('borrower.mails.borrower-comment-notification.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'          => $borrower->getUser()->getEmail(),
                'subject'     => \Lang::get('borrower.mails.borrower-comment-notification.subject', $parameters, $local),
                'templateId'  => \Setting::get('sendwithus.comments-borrower-template-id'),
                'footer'      => \Lang::get('borrower.mails.borrower-comment-notification.footer', $parameters, $local),
                'button_url'  => route('loan:index', $loan->getId()),
                'button_text' => \Lang::get('borrower.mails.borrower-comment-notification.button-text', $parameters, $local),
            ]
        );
    }
    
    public function sendExpiredLoanMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName'        => $borrower->getName(),
            'loanApplicationLink' => route('borrower:loan-application'),
            'tips'                => implode('<br>', array_slice(\Lang::get('borrower.loan-application.instructions'), 2, 4)),
        ];

        $body = \Lang::get('borrower.mails.loan-expired.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-expired.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendBorrowerInvite(Borrower $borrower, Invite $borrowerInvite, $borrowerName, $borrowerEmail, $subject, $message)
    {
        // TODO: use borrowerName
        $parameters = [
            'borrowLink' => route('home'),
        ];

        $message = nl2br(stripslashes(strip_tags(trim($message))));
        $subject = trim($subject);

        $link = \Lang::get('borrower.mails.invite.link', $parameters);
        $data['content'] = $message."<br/><br/>".$link;;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrowerInvite->getEmail(),
                'from'       => $borrowerEmail,
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.borrower-invite-template-id')
            ]
        );
    }
    
    public function sendAgainRepaymentReminder(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueAmount,
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
        ];

        $body = \Lang::get('borrower.mails.reminder-again.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-again.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReminder(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
        ];

        $body = \Lang::get('borrower.mails.reminder.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFinalArrearMail(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'contacts'              => nl2br($borrower->getContactsList()),
            'dueAmt'                => $dueInstallment->getAmount(),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions()
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-final.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-final.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFirstArrearMail(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount(),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions()
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-first.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-first.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanMonthlyArrearToContact($name, $email, Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'contactName'    => $name,
            'borrowerName'   => $borrower->getName(),
            'dueDays'        => round((time() - $dueInstallment->getDueDate()->getTimestamp())/(60*60*24)),
            'borrowerNumber' => $borrower->getProfile()->getPhoneNumber(),
        ];

        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $email,
                'label'      => \Lang::get('borrower.mails.loan-arrear-mediation-notification.body', $parameters, $local),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-mediation-notification.subject', [], $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanMonthlyArrearMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'contacts'     => nl2br($borrower->getContactsList()),
        ];

        $body = \Lang::get('borrower.mails.loan-arrear-reminder-monthly.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-arrear-reminder-monthly.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReminderTomorrow(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
            'paidAmt'               => $dueInstallment->getPaidAmount(),
        ];

        $body = \Lang::get('borrower.mails.reminder-advance.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-advance.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $country = $borrower->getCountry();
        $parameters = [
            'borrowerName'          => $borrower->getName(),
            'dueAmt'                => $dueAmount,
            'dueDate'               => $dueInstallment->getDueDate()->format('d-m-Y'),
            'repaymentInstructions' => $country->getRepaymentInstructions(),
            'pastDueAmt'            => $dueAmount->subtract($dueInstallment->getAmount()), //TODO
        ];

        $body = \Lang::get('borrower.mails.reminder-postDue.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.reminder-postDue.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendDisbursedLoanMail(Loan $loan)
    {
        $borrower = $loan->getBorrower();
        $local = $borrower->getCountry()->getLanguageCode();
        $disbursedAmount = $loan->getDisbursedAmount();
        $registrationFee = $borrower->getCountry()->getRegistrationFee();
        $repaymentInstruction = '';
        $countryInstruction = $borrower->getCountry()->getRepaymentInstructions();
        if (!empty($countryInstruction)) {
            $repaymentInstruction = nl2br($countryInstruction);
        }
        $parameters = [
            'borrowerName'         => $borrower->getName(),
            'disbursedAmount'      => $disbursedAmount,
            'registrationFee'      => $registrationFee,
            'netAmount'            => $disbursedAmount->subtract($registrationFee),
            'zidishaLink'          => route('home'),
            'repaymentInstruction' => $repaymentInstruction
        ];

        $data['image_src'] = $borrower->getUser()->getProfilePictureUrl();
        $body = \Lang::get('borrower.mails.loan-disbursed.body', $parameters, $local);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-disbursed.subject', $parameters, $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendLoanFullyFundedMail(Loan $loan)
    {
        $borrower = $loan->getBorrower();
        $local = $borrower->getCountry()->getLanguageCode();
        $deadlineDays = \Setting::get('loan.deadline');
        $appliedAt = Carbon::instance($loan->getAppliedAt());
        $expireDate = $appliedAt->addDays($deadlineDays);
        $message = \Lang::get('borrower.mails.loan-fully-funded.body', ['borrowerName' => $borrower->getName(), 'loanApplicationPage' => route('loan:index', $loan->getId()), 'expiryDate' => $expireDate->toFormattedDateString()], $local);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $loan->getBorrower()->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.loan-fully-funded.subject', [], $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendRepaymentReceiptMail(Borrower $borrower, Money $amount)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'repaidAmount' => $amount,
        ];
        $message = \Lang::get('borrower.mails.payment-receipt.body', $parameters, $local);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.payment-receipt.subject', [], $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }

    public function sendEligibleInviteMail(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'zidishaLink'  => route('home'),
        ];
        $message = \Lang::get('borrower.mails.eligible-invite.body', $parameters, $local);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $borrower->getUser()->getEmail(),
                'subject'    => \Lang::get('borrower.mails.eligible-invite.subject', [], $local),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id')
            ]
        );
    }
}
