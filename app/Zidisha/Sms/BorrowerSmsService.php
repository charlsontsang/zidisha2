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
        $arguments = [
            'borrowerName'        => $contact->getBorrower()->getName(),
            'borrowerPhoneNumber' => $contact->getBorrower()->getProfile()->getPhoneNumber(),
            'contactName'         => $contact->getName(),
        ];
        $text = \Lang::get('borrowerJoin.sms.contact-confirmation', $arguments);

        $this->smsService->send($contact->getPhoneNumber(), $text, $contact->getBorrower()->getCountry()->getCountryCode());
    }

    public function sendLoanFinalArrearNotification(Borrower $borrower, Loan $loan)
    {
        //TODO: sendLoanFinalArrearNotification

    }

    public function sendLoanFirstArrearNotification(Borrower $borrower, Loan $loan)
    {
        //TODO: sendLoanFirstArrearNotification
    }

    public function sendLoanMonthlyArrearNotificationToContact(Contact $contact, Loan $loan)
    {
        //TODO: sendLoanMonthlyArrearNotificationToContacts
    }

    public function sendLoanMonthlyArrearNotification(Borrower $borrower, Loan $loan)
    {
        //TODO: sendLoanMonthlyArrearNotification        
    }

    public function sendRepaymentReminderTommorow(Borrower $borrower, Installment $installment)
    {
        //TODO: sendRepaymentReminderTommorowSms
    }

    public function sendRepaymentReminder(Borrower $borrower, Installment $installment)
    {
        //TODO: sendRepaymentReminder
    }

    public function sendRepaymentReminderForDueAmount(Borrower $borrower, Loan $loan, $amounts)
    {
        //TODO : sendRepaymentReminderForDueAmount
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
        
        //TODO: Send Sms to borrower

    }
}
