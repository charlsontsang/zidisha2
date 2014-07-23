<?php

namespace Zidisha\Comment\Form;

use Zidisha\Form\AbstractForm;

class TranslateCommentForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'comment_id' => 'required',
            'message'    => 'required'
        ];
    }
}
