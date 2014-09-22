<?php

namespace Zidisha\Statistic;


use Carbon\Carbon;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Loan\ForgivenessLoanQuery;
use Zidisha\Loan\ForgivenessLoanShareQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Vendor\PropelDB;

class StatisticsService
{
    private $loanService;

    public function __construct(LoanService $loanService)
    {

        $this->loanService = $loanService;
    }

    public function getStatistics($name, Carbon $date, $countryId=null)
    {
        $maxDate = StatisticQuery::create()
            ->filterByCountryId($countryId)
            ->filterByName($name)
            ->select('maxDate')
            ->withColumn('max(date)', 'maxDate')
            ->findOne();

        if($date->getTimestamp()-$maxDate> 24*60*60){
            return false;
        }else{
            return StatisticQuery::create()
                ->filterByName($name)
                ->filterByCountryId($countryId)
                ->filterByDate($maxDate)
                ->select('value')
                ->findOne();
        }
    }

    public function getTotalStatistics() {
        return array(
            'raised_count'            => $this->getLoansRaisedCount(),
            'disbursed_amount'        => $this->getLoansDisbursedAmount(),
//            'average_lender_interest' => $this->getLoansRaisedAverageInterest(),
            'lenders_count'           => $this->getLendersCount(),
            'borrowers_count'         => $this->getBorrowersCount(),
            'countries_count'         => $this->getUserCountriesCount()
        );
    }

    public function getLoansRaisedCount($countryId = null, $startDate = null)
    {
        $params = [
            'loanActive' => Loan::ACTIVE,
            'loanRepaid' => Loan::REPAID,
            'loanDefaulted' => Loan::DEFAULTED
            ];

        if ($countryId) {
            $sql = 'SELECT COUNT(l.borrower_id)
                    FROM loans AS l JOIN borrowers AS b ON l.borrower_id = b.id
                    WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted)
                    AND l.deleted_by_admin = FALSE
                    AND b.country_id= :countryId';
            $params['countryId'] = $countryId;
        } else {
            $sql = 'SELECT COUNT(l.borrower_id) FROM loans AS l
                    WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted)
                    AND l.deleted_by_admin = FALSE';
        }
        if ($startDate) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $startDate;
        }

        return PropelDB::fetchNumber($sql, $params);
    }

    public function getLoansDisbursedAmount($countryId = null, $startDate = null)
    {
        $params = [
            'loanActive' => Loan::ACTIVE,
            'loanRepaid' => Loan::REPAID,
            'loanDefaulted' => Loan::DEFAULTED
        ];

        $sql = 'SELECT SUM(l.disbursed_amount / r.rate)
                FROM loans l
                JOIN (SELECT e.currency_code, e.rate
                      FROM exchange_rates e
                      JOIN (SELECT currency_code, MAX(start_date) as max_start
                              FROM exchange_rates GROUP BY currency_code) ee
                        ON e.currency_code = ee.currency_code AND e.start_date = ee.max_start) r
                ON r.currency_code = l.currency_code
                WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted)
                AND l.deleted_by_admin = FALSE';

        if ($countryId) {
            $sql .= ' AND b.country_id= :countryId';
            $params['countryId'] = $countryId;
        }

        if ($startDate) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $startDate;
        }

        return PropelDB::fetchNumber($sql, $params);
    }

    public function getLendersCount()
    {
        return PropelDB::fetchNumber('SELECT COUNT(id) FROM lenders WHERE active = TRUE');
    }

    public function getBorrowersCount()
    {
        return PropelDB::fetchNumber('SELECT COUNT(id) FROM borrowers WHERE active = TRUE');
    }

    public function getUserCountriesCount()
    {
        $sql = 'SELECT DISTINCT country_id FROM borrowers WHERE active = TRUE
              UNION
              SELECT DISTINCT country_id FROM lenders WHERE active = TRUE';

        return count(PropelDB::fetchAll($sql));
    }

    public function getLendingStatistics($startDate = null, $countryId = null) {

        $statistics =  array(
            'raised_count' => $this->getLoansRaisedCount($countryId, $startDate),
            'average_lender_interest' => $this->getLoansRaisedAverageInterest($countryId, $startDate),
        );

        $params = [
            'loanActive' => Loan::ACTIVE,
            'loanRepaid' => Loan::REPAID,
            'loanDefaulted' => Loan::DEFAULTED,
        ];
        if ($countryId) {
            $sql = 'SELECT l.disbursed_amount, l.id, l.borrower_id, l.status
                  FROM loans AS l JOIN borrowers AS b ON l.borrower_id = b.id
                  WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted)
                  AND l.deleted_by_admin = FALSE
                  AND b.country_id= :countryId';
            $params['countryId'] = $countryId;
        } else {
            $sql = 'SELECT l.disbursed_amount, l.id, l.borrower_id, l.status
                  FROM loans AS l
                  WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted)
                  AND l.deleted_by_admin = FALSE';
        }
        if ($startDate) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $startDate;
        }

        $date=Carbon::now();
        $disb_amount=0;
        $repaid_amountUsd=0;
        $forgiveAmountUsd=0;
        $defaultAmountUsd=0;
        $principalOutstandingUsd=0;
        $principalOutstandingOnTimeUsd=0;
        $principalOutstandingLateUsd=0;

        $offset = 0;
        $limit = 1000;
        while ($offset < $statistics['raised_count']) {
            // cannot bind the offset and limit parameters as strings (in $stmt->execute)
            $sqlLimitOffset = "$sql LIMIT $limit OFFSET $offset";
            $rows = PropelDB::fetchAll($sqlLimitOffset, $params);

            if (empty($rows) || is_object($rows)) {
                break;
            }

            foreach($rows as $row) {
                $borrower = BorrowerQuery::create()
                    ->findOneById($row['borrower_id']);
                $exchangeRate = ExchangeRateQuery::create()
                    ->findCurrent($borrower->getCountry()->getCurrency())->getRate();

                $disb_amount += ($row['disbursed_amount'] / $exchangeRate);

                switch ($row['status']) {
                    case Loan::ACTIVE:
                        $threshold = 10;
                        $o = "SELECT max(id) from installments where loan_id = :loanId AND borrower_id= :borrowerId";
                        $maxid = PropelDB::fetchNumber($o, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id']]);

                        $p = "SELECT SUM(amount) as totamt from installments where loan_id = :loanId AND borrower_id = :borrowerId AND due_date < :dueDate";
                        $totamt = PropelDB::fetchNumber($p, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'dueDate' => $date]);

                        $q = "SELECT SUM(paid_amount) as totpaidamt from installment_payments where loan_id = :loanId AND borrower_id = :borrowerId AND paid_date < :paidDate";
                        $totpaidamt = PropelDB::fetchNumber($q, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'paidDate' => $date]);

                        $r = "SELECT id from installment_payments where loan_id = :loanId AND borrower_id = :borrowerId AND paid_date < :paidDate order by id desc";
                        $rid = PropelDB::fetchNumber($r, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'paidDate' => $date]);

                        $t="SELECT sum(amount) from forgiveness_loan_shares where loan_id = :loanId AND borrower_id= :borrowerId AND date < :date";
                        $forgiveAmount = PropelDB::fetchNumber($t, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'date' => $date]);

                        $ratio = $this->loanService->getPrincipalRatio($row['id']);

                        $thresholdNative=$threshold * $exchangeRate;
                        if($forgiveAmount) {
                            $forgivePrinAmount = $forgiveAmount * $ratio;
                            $row['prinAmount'] = $row['disbursed_amount'] - $forgivePrinAmount;
                            $forgiveAmountUsd += $forgiveAmount / $exchangeRate;
                        } else {
                            $row['prinAmount'] = $row['disbursed_amount'];
                        }
                        $row['principlePaid']= ($totpaidamt * $ratio);
                        $row['principleOutstanding']= $row['prinAmount']-$row['principlePaid'];
                        $row['dueAmount']= $totamt-$totpaidamt;
                        $row['dueAmountUSD']= ($row['dueAmount'] / $exchangeRate);
                        if($row['dueAmountUSD'] <$threshold) {
                            // this amount will not considor in repayrepot using threshold functionality
//                            continue;
                        }
                        if($rid==$maxid) {
                            $r="SELECT max(due_date) as duedate from installments where loan_id = :loanId AND borrower_id= :borrowerId";
                            $duedate = PropelDB::fetchOne($r, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id']]);
                        } elseif(empty($rid)) {
                            $r="SELECT due_date from installments where loan_id = :loanId AND borrower_id= :borrowerId AND amount > :amount AND paid_amount is NULL order by id";
                            $duedate = PropelDB::fetchOne($r, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'amount' => 0]);
                        } else {
                            $s="SELECT SUM(amount) from installments where loan_id = :loanId AND borrower_id = :borrowerId AND id <= :Id";
                            $totAmtToRid = PropelDB::fetchNumber($s, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'Id' => $rid]);

                            if(($totpaidamt + $thresholdNative) < $totAmtToRid) {
                                $r="SELECT due_date from installments where loan_id = :loanId AND borrower_id = :borrowerId AND id = :Id";
                                $duedate = PropelDB::fetchOne($r, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id'], 'Id' => $rid]);
                            } else {
                                $r="SELECT * from installments where loan_id = :loanId AND borrower_id = :borrowerId";
                                $repayAll = PropelDB::fetchAll($r, ['loanId' => $row['id'], 'borrowerId' => $row['borrower_id']]);
                                $reducedAmount=$totAmtToRid - $totpaidamt;
                                foreach($repayAll as $repay) {
                                    if($repay['id'] >$rid) {
                                        if(($repay['amount']) >($thresholdNative -$reducedAmount)) {
                                            $duedate=$repay['due_date'];
                                            break;
                                        } else {
                                            $duedate=$repay['due_date'];
                                            $reducedAmount += $repay['amount'];
                                        }
                                    }
                                }
                            }
                        }
                        if (is_array($duedate)) {
                            $time = strtotime($duedate['due_date']);
                        } else {
                            $time = strtotime($duedate);
                        }
                        $carbonDate = Carbon::createFromTimestamp($time);
                        $fullDays = $date->diffInDays($carbonDate);

                        if($fullDays < 31) {
                            $principalOutstandingOnTimeUsd += $row['principleOutstanding'] / $exchangeRate;
                        } else {
                            $principalOutstandingLateUsd += $row['principleOutstanding'] / $exchangeRate;
                        }
                        $repaid_amountUsd += $row['principlePaid'] / $exchangeRate;
                        $principalOutstandingUsd += $row['principleOutstanding'] / $exchangeRate;
                        break;
                    case Loan::REPAID:
                        $isforgive = ForgivenessLoanQuery::create()
                            ->isLoanAlreadyInForgiveness($row['id']);
                        $repaid_amountUsd +=($row['disbursed_amount']/$exchangeRate);
                        if ($isforgive){
                            $forgiveAmount = ForgivenessLoanShareQuery::create()
                                ->filterByLoanId($row['id'])
                                ->filterByBorrowerId($row['borrower_id'])
                                ->select('amount')
                                ->withColumn('SUM(usdAmount)', 'amount')
                                ->findOne();
                            $forgiveAmountUsd+=$forgiveAmount;
                            $repaid_amountUsd -= $forgiveAmount;
                        }
                        break;
                    case Loan::DEFAULTED:
                        $res = InstallmentQuery::create()
                            ->filterByLoanId($row['id'])
                            ->select('amttotal', 'paidtotal')
                            ->withColumn('SUM(amount)', 'amttotal')
                            ->withColumn('SUM(paid_amount)', 'paidtotal')
                            ->findOne();

                        if($res['amttotal'] > $res['paidtotal'])
                        {
                            $amount=$res['amttotal'] - $res['paidtotal'];
                            $ratio = $this->loanService->getPrincipalRatio($row['id']);
                            $paidamount=$res['paidtotal'];
                            $defaultAmount =($amount * $ratio);
                            $defaultAmountUsd += ($defaultAmount/$exchangeRate);
                            $defaultpaidamount =($paidamount* $ratio);
                            $repaid_amountUsd += ($defaultpaidamount/$exchangeRate);
                            $forgiveAmount = ForgivenessLoanShareQuery::create()
                                ->filterByLoanId($row['id'])
                                ->filterByBorrowerId($row['borrower_id'])
                                ->select('amount')
                                ->withColumn('SUM(usdAmount)', 'amount')
                                ->findOne();

                            $netforgiveAmount=($forgiveAmount* $ratio);
                            $forgiveAmountUsd+=$netforgiveAmount;
                        }
                        break;
                }
            }
            $offset += $limit;
        }

        if ($statistics['raised_count']) {
            $statistics['repaid_amount']              = $repaid_amountUsd;
            $statistics['repaid_rate']                = $repaid_amountUsd / $disb_amount * 100;
            $statistics['outstanding_on_time_amount'] = $principalOutstandingOnTimeUsd;
            $statistics['outstanding_on_time_rate']   = $principalOutstandingOnTimeUsd / $disb_amount * 100;
            $statistics['outstanding_late_amount']    = $principalOutstandingLateUsd;
            $statistics['outstanding_late_rate']      = $principalOutstandingLateUsd / $disb_amount * 100;
            $statistics['forgiven_amount']            = $forgiveAmountUsd;
            $statistics['forgiven_rate']              = $forgiveAmountUsd / $disb_amount * 100;
            $statistics['written_off_amount']         = $defaultAmountUsd;
            $statistics['written_off_rate']           = $defaultAmountUsd / $disb_amount * 100;
            $statistics['disbursed_amount']           = $disb_amount;
        } else {
            $statistics['repaid_amount']              = 0;
            $statistics['repaid_rate']                = 0;
            $statistics['outstanding_on_time_amount'] = 0;
            $statistics['outstanding_on_time_rate']   = 0;
            $statistics['outstanding_late_amount']    = 0;
            $statistics['outstanding_late_rate']      = 0;
            $statistics['forgiven_amount']            = 0;
            $statistics['forgiven_rate']              = 0;
            $statistics['written_off_amount']         = 0;
            $statistics['written_off_rate']           = 0;
            $statistics['disbursed_amount']           = 0;
        }

        return $statistics;
    }

    private function getLoansRaisedAverageInterest($countryId, $startDate)
    {
        $params = [
            'loanActive' => Loan::ACTIVE,
            'loanRepaid' => Loan::REPAID,
            'loanDefaulted' => Loan::DEFAULTED,
            'loanFunded' => Loan::FUNDED
        ];

        if ($countryId) {
            $sql = 'SELECT AVG(l.lender_interest_rate) FROM loans AS l
                  JOIN borrowers AS b ON l.borrower_id = b.id
                  WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted, :loanFunded)
                  AND l.deleted_by_admin = FALSE
                  AND b.country_id= :countryId';
            $params['countryId'] = $countryId;
        } else {
            $sql = 'SELECT AVG(l.lender_interest_rate) FROM loans AS l
                  WHERE l.status IN (:loanActive, :loanRepaid, :loanDefaulted, :loanFunded)
                    AND l.deleted_by_admin = FALSE';
        }
        if ($startDate) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $startDate;
        }

        return PropelDB::fetchNumber($sql, $params);
    }

}
