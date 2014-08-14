<?php

namespace Zidisha\Credit;

use Zidisha\Borrower\Borrower;
use Zidisha\Credit\Base\CreditsEarnedQuery as BaseCreditsEarnedQuery;
use Zidisha\CreditSetting;
use Zidisha\Loan\ForgivenLoanQuery;
use Zidisha\Vendor\PropelDB;


/**
 * Skeleton subclass for performing query and update operations on the 'credits_earned' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class CreditsEarnedQuery extends BaseCreditsEarnedQuery
{

    public function getCurrentCredit(Borrower $borrower, $type)
    {

        $sql = 'SELECT count(id) as commentposted, SUM(credit) as credit FROM credits_earned WHERE borrower_id = :borrowerId AND loan_id = :loanId AND credit_type = :creditType';

        $currentCredit = ( PropelDB::fetchAll($sql, [
                'borrowerId' => $borrower->getId(),
                'loanId' => $borrower->getActiveLoanId(),
                'creditType' => $type,
            ]));

        if ($type == CreditSetting::ON_TIME_REPAYMENT_CREDIT) {
            $isForgiven = ForgivenLoanQuery::create()
                ->getTotalForgivenLendersForLoan($borrower->getActiveLoan());
            if ($isForgiven > 0) {
                $currentCredit['credit'] = 0;
            }
        }

        return $currentCredit;
    }
} // CreditsEarnedQuery
