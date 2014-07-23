<?php

namespace Zidisha\Comment\Form;


use Zidisha\Form\AbstractForm;

class ReplyCommentForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'message'   => 'required',
            'parent_id' => 'required'

        ];
    }
}
