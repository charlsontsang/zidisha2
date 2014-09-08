<?php

namespace Zidisha\Loan;

use Zidisha\Loan\Base\ForgivenessLoanQuery as BaseForgivenessLoanQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'forgiveness_loans' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ForgivenessLoanQuery extends BaseForgivenessLoanQuery
{
    public function isLoanAlreadyInForgiveness($loanId)
    {
        return $this
            ->filterByLoanId($loanId)
            ->count();
    }
} // ForgivenessLoanQuery
