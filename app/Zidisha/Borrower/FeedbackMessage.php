<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\FeedbackMessage as BaseFeedbackMessage;

class FeedbackMessage extends BaseFeedbackMessage
{

    const LOAN_TYPE = 'loan';
    const ACTIVATION_TYPE = 'activation';

    const COLUMN_LOAN_TYPE = 0;
    const COLUMN_ACTIVATION_TYPE = 1;

    public function setCc($cc)
    {
        $emails = explode(',', $cc);
        $ccEmails = [];
        foreach ($emails as $email) {
            $ccEmails[] = trim($email);
        }

        return parent::setCc(implode(',', $ccEmails));
    }
    
    public function getCcEmails()
    {
        return $this->getCc() ? explode(',', $this->getCc()) : [];
    }
    
}
