<?php

namespace Zidisha\Loan;

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

} // LoanQuery
