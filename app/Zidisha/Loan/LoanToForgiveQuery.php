<?php

namespace Zidisha\Loan;

use Zidisha\Loan\Base\LoanToForgiveQuery as BaseLoanToForgiveQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'loans_to_forgive' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LoanToForgiveQuery extends BaseLoanToForgiveQuery
{
    public function isLoanAlreadyInForgiveness($loanid)
    {
        return $this
            ->filterByLoanId($loanid)
            ->count();
    }
} // LoanToForgiveQuery
