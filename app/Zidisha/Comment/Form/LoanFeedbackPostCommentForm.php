<?php
namespace Zidisha\Comment\Form;

class LoanFeedbackPostCommentForm extends PostCommentForm
{
    public function getRules($data)
    {
        return [
            'message' => 'required',
            'rating'  => 'required|checkCommentRating',
        ];
    }
} 
