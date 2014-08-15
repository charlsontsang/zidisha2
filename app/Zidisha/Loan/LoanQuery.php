<?php

namespace Zidisha\Loan;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\Borrower;
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

    public function getNumberOfLoansByInvitees($AcceptedInviteesIds)
    {
        return $this
            ->useBidQuery()
                ->filterByLenderId($AcceptedInviteesIds, Criteria::IN)
                ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();
    }

    public function getNumberOfLoansByRecipients($RedeemedGiftCardsRecipientsIds)
    {
        return $this
            ->useBidQuery()
                ->filterByLenderId($RedeemedGiftCardsRecipientsIds, Criteria::IN)
                ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();
    }

    public function getNumberOfLoansForLender(Lender $lender)
    {
        return $this
            ->useBidQuery()
                ->filterByLender($lender)
                ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();
    }

    public function getLastLoan(Borrower $borrower) {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->orderById('DESC')
            ->findOne();
    }

    public function getLastRepaidLoan(Borrower $borrower)
    {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->filterByStatus([Loan::REPAID, Loan::DEFAULTED])
            ->filterByExpiredAt(null)
            ->orderById('DESC')
            ->findOne();
    }

    public function isFirstFundedLoan(Borrower $borrower)
    {
        $count =  $this
            ->filterByBorrower($borrower)
            ->filterByStatus([Loan::ACTIVE, Loan::REPAID, Loan::DEFAULTED])
            ->count();

        if ($count) {
            return false;
        }
        return true;
    }

    public function getAllRepaidLoansForBorrower(Borrower $borrower)
    {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->filterByStatus(Loan::REPAID)
            ->find();
    }
} // LoanQuery
