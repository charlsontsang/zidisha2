<?php
namespace Zidisha\Comment\Form;

use Zidisha\Comment\LoanFeedbackComment;

class LoanFeedbackPostCommentForm extends PostCommentForm
{
    public function getRules($data)
    {
        return [
            'message' => 'required',
            'rating'  => 'required|in:' . $this->getRatingTypes(),
        ];
    }

    public function getRatingTypes()
    {
        return LoanFeedbackComment::NEUTRAL . ',' .
               LoanFeedbackComment::NEGATIVE . ',' .
               LoanFeedbackComment::POSITIVE;
    }
} 
