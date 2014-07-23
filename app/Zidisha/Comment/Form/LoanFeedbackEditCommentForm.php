<?php
namespace Zidisha\Comment\Form;

use Zidisha\Comment\LoanFeedbackComment;

class LoanFeedbackEditCommentForm extends EditCommentForm
{
    public function getRules($data)
    {
        return [
            'message'    => 'required',
            'rating'     => 'required|in:' . $this->getRatingTypes(),
            'comment_id' => 'required',
        ];
    }

    public function getRatingTypes()
    {
        return LoanFeedbackComment::NEUTRAL . ',' .
        LoanFeedbackComment::NEGATIVE . ',' .
        LoanFeedbackComment::POSITIVE;
    }
} 
