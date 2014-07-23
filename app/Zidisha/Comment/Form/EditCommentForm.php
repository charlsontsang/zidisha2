<?php
namespace Zidisha\Comment\Form;

use Zidisha\Form\AbstractForm;

class EditCommentForm extends AbstractForm
{
    public function getRules($data)
    {
        return [
            'message' => 'required'
        ];
    }
}
