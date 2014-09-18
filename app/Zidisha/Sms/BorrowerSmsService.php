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
        $local = $contact->getBorrower()->getCountry()->getLanguageCode();
        $parameters = [
            'borrowerName'        => $contact->getBorrower()->getName(),
            'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
            'contactName'         => $contact->getName(),
        ];
        $data = [
            'countryCode' => $contact->getBorrower()->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.contact-confirmation', $parameters, $local),
        ];
        $this->smsService->queue($contact->getPhoneNumber(), $data);
    }

    public function sendLoanFinalArrearNotification(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'contacts'     => nl2br($borrower->getContactsList()),
            'dueAmt'       => $dueInstallment->getAmount(),
            'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.final-arrear-notification', $parameters, $local),
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendLoanFirstArrearNotification(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'dueAmt'       => $dueInstallment->getAmount(),
            'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.first-arrear-notification', $parameters, $local)
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendLoanMonthlyArrearNotificationToContact(Contact $contact, Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'contactName'    => $contact->getName(),
            'borrowerName'   => $borrower->getName(),
            'dueDays'        => round((time() - $dueInstallment->getDueDate()->getTimestamp()) / (60 * 60 * 24)),
            'borrowerNumber' => $profile->getPhoneNumber(),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'       => \Lang::get('borrower.sms.loan-arrear-mediation-notification', $parameters, $local)
        ];
        $this->smsService->send($contact->getPhoneNumber(), $data);
    }

    public function sendLoanMonthlyArrearNotification(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'contacts'     => nl2br($borrower->getContactsList()),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \lang::get('borrower.sms.loan-arrear-reminder-monthly', $parameters, $local)
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminderTomorrow(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'dueAmt'  => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.repayment-reminder', $parameters, $local),
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminder(Borrower $borrower, Installment $dueInstallment)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'dueAmt'  => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
            'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.repayment-reminder', $parameters, $local)
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Installment $dueInstallment, Money $dueAmount)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'dueAmt'  => $dueAmount,
            'dueDate' => $dueInstallment->getDueDate()->format('d-m-Y'),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.repayment-reminder', $parameters, $local)
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }

    public function sendRepaymentReceiptSms(Borrower $borrower, Money $amount)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'paidAmount' => (string) $amount,
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.payment-receipt', $parameters, $local)
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    public function sendEligibleInviteSms(Borrower $borrower)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.eligible-invite', [], $local)
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    public function sendBorrowerCommentNotificationSms(Borrower $borrower , Comment $comment, $postedBy)
    {
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'postedBy' => $postedBy,
            'message'  => nl2br($comment->getMessage()),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.borrower-comment-notification', $parameters, $local)
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }

    public function sendInviteAlertSms(Invite $invite)
    {
        $borrower = $invite->getBorrower();
        $invitee = $invite->getInvitee();
        $local = $borrower->getCountry()->getLanguageCode();
        $profile = $borrower->getProfile();
        $parameters = [
            'borrowerName'      => $borrower->getName(),
            'newBorrowerName'   => $invitee->getName(),
            'newBorrowerNumber' => $invitee->getProfile()->getPhoneNumber(),
        ];
        $data = [
            'countryCode' => $borrower->getCountry()->getCountryCode(),
            'content'     => \Lang::get('borrower.sms.invite-alert', $parameters, $local)
        ];
        $this->smsService->queue($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->queue($alternateNumber, $data);
        }
    }
}
