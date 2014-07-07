<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;

class TranslateForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'translateAboutMe' => '',
            'translateAboutBusiness' => '',
            'translateProposal' => '',
        ];
    }

    public function getDefaultData()
    {

    }

}