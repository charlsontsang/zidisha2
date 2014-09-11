<?php
namespace Zidisha\Comment\Form;

use Zidisha\Comment\Comment;
use Zidisha\Form\AbstractForm;

class EditCommentForm extends AbstractForm
{
    
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
    
    public function getRules($data)
    {
        return [
            'message' => 'required'
        ];
    }
}
