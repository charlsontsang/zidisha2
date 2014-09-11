<?php
namespace Zidisha\Comment\Form;

use Zidisha\Comment\LoanFeedbackComment;

class LoanFeedbackEditCommentForm extends EditCommentForm
{
    public function getRules($data)
    {
        $rules = [
            'message'    => 'required',
            'comment_id' => 'required',
        ];
        
        if ($this->comment->isRoot()) {
            $rules['rating'] = 'required|in:' . $this->getRatingTypes();
        }
        
        return $rules;
    }

    public function getRatingTypes()
    {
        return LoanFeedbackComment::NEUTRAL . ',' .
        LoanFeedbackComment::NEGATIVE . ',' .
        LoanFeedbackComment::POSITIVE;
    }
} 
