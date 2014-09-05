<?php

namespace Zidisha\Loan;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Validator\Constraints\Currency;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Money;
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
    public function filterCompleted()
    {
        return $this->filterByStatus([Loan::REPAID, Loan::DEFAULTED]);
    }

    public function filterDisbursed()
    {
        return $this->filterByStatus([Loan::ACTIVE, Loan::REPAID, Loan::DEFAULTED]);
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
        return $this->where('NOT EXISTS (SELECT NULL FROM forgiveness_loan_shares fl WHERE loans.id = fl.loan_id AND fl.lender_id = ?)', $lender->getId());
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

    public function findLastLoan(Borrower $borrower) {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->orderById('DESC')
            ->findOne();
    }

    public function findLastCompletedLoan(Borrower $borrower)
    {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->filterCompleted()
            ->orderById('DESC')
            ->findOne();
    }

    public function hasDisbursedLoan(Borrower $borrower)
    {
        $count =  $this
            ->filterByBorrower($borrower)
            ->filterDisbursed()
            ->count();

        return $count > 0;
    }

    public function getAllRepaidLoansForBorrower(Borrower $borrower)
    {
        return $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->filterByStatus(Loan::REPAID)
            ->find();
    }

    public function getMaximumRepaidDisbursedAmount(Borrower $borrower, $currencyCode)
    {
        $amount = $this
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->filterByStatus(Loan::REPAID)
            ->select('AmountRaised')
            ->withColumn('MAX(disbursed_amount)', 'AmountRaised')
            ->findOne();

        return Money::create($amount ?: 0, $currencyCode);
    }

    public function filterByAutoLendableLoan()
    {
        $autoLendedBids = BidQuery::create()
            ->filterByLoan($this)
            ->filterByIsAutomatedLending(true)
            ->count();
        
        $loanBids = BidQuery::create()
            ->filterByLoan($this)
            ->filterByIsAutomatedLending(false)
            ->count();
        
        if ($loanBids > $autoLendedBids) {
            return true;
        }
        
        return false;
    }

    public function filterByLoanWithHighFeedbackComment()
    {
        
        return $this;
    }

    public function getAllLoansForBorrower(Borrower $borrower)
    {
        return $this
            ->select('id')
            ->filterByBorrower($borrower)
            ->filterByDeletedByAdmin(false)
            ->find()
            ->toArray();
    }

} // LoanQuery
