<?php

namespace Zidisha\Loan;

use Zidisha\Lender\Lender;
use Zidisha\Loan\Base\LoanQuery as BaseLoanQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'loan' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LoanQuery extends BaseLoanQuery
{
    public function filterEnded()
    {
        return $this->filterByStatus([Loan::REPAID, Loan::DEFAULTED]);
    }

    public function filterActive()
    {
        return $this->filterByStatus([Loan::ACTIVE, Loan::FUNDED]);
    }

    public function orderByRand() {
        return $this->addAscendingOrderByColumn('random( )');
    }

    public function filterFundRaising()
    {
        return $this->filterByStatus(Loan::OPEN);
    }

    public function filterNotForgivenByLender(Lender $lender)
    {
        return $this->where('NOT EXISTS (SELECT NULL FROM forgiven_loans fl WHERE loans.id = fl.loan_id AND fl.lender_id = ?)', $lender->getId());
    }

    public function getRepaidAmounts(Lender $lender)
    {

    }

} // LoanQuery
