<?php

namespace Zidisha\Borrower;


use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Loan\Loan;
use Zidisha\Vendor\PropelDB;

class VolunteerMentorService {

    public function getMentorPendingMembers(Borrower $borrower)
    {
        $minDate = Carbon::now()->subMonths(3);

        $query = "SELECT id FROM borrowers as b, borrower_profiles as bp, borrower_join_logs as log
                    WHERE (
                    (
                    (b.activation_status = 1 || b.activation_status = 0 || b.loan_status = 4)
                    AND log.verified_at > :date)
                    || b.loan_status = 0 || b.loan_status = 1)
                    AND b.activation_status != 3
                    AND b.id = bp.borrower_id
                    AND b.id = log.borrower_id
                    AND b.volunteer_mentor_id = :mentorId";

        $memberIds = PropelDB::fetchAll($query, ['mentorId' => $borrower->getId(), 'date' => $minDate]);
        $ids = array();
        if (!empty($memberIds)) {
            foreach ($memberIds as $member) {
                array_push($ids, $member['id']);
            }
        }

        $membersWithDefaultedLoans = BorrowerQuery::create()
            ->filterById($ids)
            ->useLoanRelatedByBorrowerIdQuery()
                ->filterByStatus(Loan::DEFAULTED)
            ->endUse()
            ->groupById()
            ->find();

        $membersWithOutDefaultedLoans = BorrowerQuery::create()
            ->filterById($ids)
            ->orderById('DESC')
            ->find();

        $members = array_diff($membersWithOutDefaultedLoans->getData(), $membersWithDefaultedLoans->getData());

        return $members;
    }

    public function getMentorAssignedMembers(Borrower $borrower)
    {
        return BorrowerQuery::create()
            ->filterByActive(true)
            ->filterByVolunteerMentorId($borrower->getId())
            ->filterByLastLoanId(null, Criteria::NOT_EQUAL)
            ->filterByActivationStatus(Borrower::ACTIVATION_APPROVED)
            ->orderById('DESC')
            ->find();
    }
}
