<?php

namespace Zidisha\Sms;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Contact;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;

class BorrowerSmsService {

    private $smsService;

    public function __construct(SmsService $smsService){

        $this->smsService = $smsService;
    }

    public function sendBorrowerJoinedContactConfirmationSms(Contact $contact)
    {
        $data = [
            'parameters' => [
                'borrowerName'        => $contact->getBorrower()->getName(),
                'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
                'contactName'         => $contact->getName(),
            ],
            'countryCode'         => $contact->getBorrower()->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.contact-confirmation'
        ];
        $this->smsService->send($contact->getPhoneNumber(), $data);
    }

    public function sendLoanFinalArrearNotification(Borrower $borrower, Installment $dueInstallment)
    {
        $profile = $borrower->getProfile();
        $data = [
            'parameters' => [
                'borrowerName' => $borrower->getName(),
                'contacts'     => nl2br($borrower->getContactsList()),
                'dueAmt'       => $dueInstallment->getAmount(),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.final-arrear-notification'
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
            'parameters' => [
                'borrowerName' => $borrower->getName(),
                'dueAmt'       => $dueInstallment->getAmount(),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.first-arrear-notification'
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
            'parameters' => [
                'contactName'    => $contact->getName(),
                'borrowerName'   => $borrower->getName(),
                'dueDays'        => round((time() - $dueInstallment->getDueDate()->getTimestamp())/(60*60*24)),
                'borrowerNumber' => $profile->getPhoneNumber(),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.loan-arrear-mediation-notification'
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
            'parameters' => [
                'dueAmt'       => $dueInstallment->getAmount()->subtract($dueInstallment->getPaidAmount()),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.repayment-reminder'
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
            'parameters' => [
                'dueAmt'       => $dueInstallment->getAmount(),
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.repayment-reminder'
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
            'parameters' => [
                'dueAmt'       => $dueAmount,
                'dueDate'      => $dueInstallment->getDueDate()->format('d-m-Y'),
            ],
            'countryCode'         => $borrower->getCountry()->getCountryCode(),
            'label'               => 'borrower.sms.repayment-reminder'
        ];
        $this->smsService->send($profile->getPhoneNumber(), $data);

        $alternateNumber = $profile->getAlternatePhoneNumber();
        if ($alternateNumber) {
            $this->smsService->send($alternateNumber, $data);
        }
    }
}
