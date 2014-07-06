<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\FeedbackMessageQuery as BaseFeedbackMessageQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'borrower_feedback_messages' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FeedbackMessageQuery extends BaseFeedbackMessageQuery
{

    public function filterByActivationType()
    {
        return $this->filterByType(FeedbackMessage::ACTIVATION_TYPE);
    }
    
} // FeedbackMessageQuery
