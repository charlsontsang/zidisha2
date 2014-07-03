<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;

class FeatureFeedbackForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'borrowerEmail'      => 'required|email',
            'cc'         => '',
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
        return \Lang::get('text.admin.suggestion-default-subject');
    }

    public function getMessage($name)
    {
        return  \Lang::get('text.admin.suggestion-default-message', array('name' => $name));
    }

}
