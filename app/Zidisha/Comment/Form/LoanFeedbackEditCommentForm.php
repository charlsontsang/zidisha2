<?php
namespace Zidisha\Comment\Form;

class LoanFeedbackEditCommentForm extends EditCommentForm
{
    public function getRules($data)
    {
        return [
            'message'    => 'required',
            'rating'     => 'required',
            'comment_id' => 'required',
        ];
    }
} 
