<?php

namespace Zidisha\Borrower\Form;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\InviteQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Vendor\PropelDB;

class InviteForm extends AbstractForm
{
    private $borrower;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }
    public function getRules($data)
    {
        return [
            'email'  => 'required|email|uniqueEmail|not_in:' . implode(',', $this->getInvitedEmails()),
            'borrowerName' => 'required',
            'borrowerEmail' => 'required|email|uniqueUserEmail:' . $this->borrower->getId(),
            'subject' => 'required',
            'note'    => 'required|min:1',
        ];
    }

    public function getDefaultData()
    {
        return [
            'borrowerName' => $this->borrower->getName(),
            'borrowerEmail' => $this->borrower->getUser()->getEmail(),
            'subject' => '',
        ];
    }

    public function getInvitedEmails()
    {
        $emails = InviteQuery::create()
            ->filterByBorrower($this->borrower)
            ->withColumn('email')
            ->find();

        return $emails->getData();
    }
}
