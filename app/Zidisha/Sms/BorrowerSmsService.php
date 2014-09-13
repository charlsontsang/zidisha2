<?php

namespace Zidisha\Sms;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Contact;
use Zidisha\Borrower\Invite;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;

class BorrowerSmsService
{

    private $smsService;

    public function __construct(SmsService $smsService)
    {

        $this->smsService = $smsService;
    }

    public function sendBorrowerJoinedContactConfirmationSms(Contact $contact)
    {
        $data = [
            'parameters'  => [
                'borrowerName'        => $contact->getBorrower()->getName(),
                'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
                'contactName'         => $contact->getName(),
            ],
            'countryCode' => $contact->getBorrower()->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.contact-confirmation'
        ];
        $this->smsService->queue($contact->getPhoneNumber(), $data);
    }

    public function sendLoanFinalArrearNotification(Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'borrowerName' => $borrower->getName(),
                'contacts'     => nl2br($borrower->getContactsList()),
                'dueAmt'       => $dueInstallment->getAmount(),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.final-arrear-notification'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendLoanFirstArrearNotification(Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'borrowerName' => $borrower->getName(),
                'dueAmt'       => $dueInstallment->getAmount(),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.first-arrear-notification'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendLoanMonthlyArrearNotificationToContact(Contact $contact, Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'contactName'    => $contact->getName(),
                'borrowerName'   => $borrower->getName(),
                'dueDays'        => round((time() - $dueInstallment->getDueDate()->getTimestamp()) / (60 * 60 * 24)),
                'borrowerNumber' => $profile->getPhoneNumber(),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.loan-arrear-mediation-notification'
        ];
        $this->smsService->send($contact->getPhoneNumber(), $data);
    }

    public function sendLoanMonthlyArrearNotification(Borrower $borrower)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'borrowerName' => $borrower->getName(),
                'contacts'     => nl2br($borrower->getContactsList()),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.loan-arrear-reminder-monthly'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminderTomorrow(Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'dueAmt'  => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
                'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.repayment-reminder'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminder(Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'dueAmt'  => $dueInstallment->getAmount(),
                'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.repayment-reminder'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'dueAmt'  => $dueAmount,
                'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.repayment-reminder'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReceiptSms(Borrower $borrower, Money $amount)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'paidAmount' => (string) $amount,
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.payment-receipt'
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    public function sendEligibleInviteSms(Borrower $borrower)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.eligible-invite'
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    public function sendBorrowerCommentNotificationSms(Borrower $borrower , Comment $comment, $postedBy)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'postedBy' => $postedBy,
                'message'  => nl2br($comment->getMessage()),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.borrower-comment-notification'
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    //TODO use it when an borrower invite get accepted
    public function sendInviteAlertSms(Invite $invite)
    {
        $borrower = $invite->getBorrower();
        $invitee = $invite->getInvitee();
        $profile = $borrower->getProfile();
        $data = [
            'parameters'  => [
                'borrowerName'      => $borrower->getName(),
                'newBorrowerName'   => $invitee->getName(),
                'newBorrowerNumber' => $invitee->getProfile()->getPhoneNumber(),
            ],
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'label'       => 'borrower.sms.invite-alert'
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }
}
