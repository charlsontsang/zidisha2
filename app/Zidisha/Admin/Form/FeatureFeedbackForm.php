<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;

class FeatureFeedbackForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'borrowerEmail'      => 'required|email',
            'cc'         => 'Emails',
            'replyTo' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
            'senderName' => 'required',
        ];
    }

    public function getDefaultData()
    {

        return [
            'cc'         => '',
            'replyTo' => 'service@zidisha.org',
            'senderName' => 'admin',
        ];
    }

    public function getSubject()
    {
        return \Lang::get('borrower.mails.profile-suggestions.suggestion-default-subject');
    }

    public function getMessage($name)
    {
        return  \Lang::get('borrower.mails.profile-suggestions.suggestion-default-message', array('name' => $name));
    }
}
