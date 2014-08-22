<?php

namespace Zidisha\Statistic;


use Zidisha\Loan\Loan;
use Zidisha\Vendor\PropelDB;

class StatisticsService
{

    public function getStatistics($name, $date, $countryId=null)
    {
        $maxDate = StatisticsQuery::create()
            ->filterByCountryId($countryId)
            ->filterByName($name)
            ->select('maxDate')
            ->withColumn('max(date)', 'maxDate')
            ->findOne();

        if($date-$maxDate> 24*60*60){
            return false;
        }else{
            return StatisticsQuery::create()
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

    public function getLoansRaisedCount($countryId = null, $start_date = null)
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
        if ($start_date) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $start_date;
        }

        return PropelDB::fetchNumber($sql, $params);
    }

    public function getLoansDisbursedAmount($countryId = null, $start_date = null)
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

        if ($start_date) {
            $sql .= ' AND l.accepted_at >= :acceptedAt';
            $params['acceptedAt'] = $start_date;
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

    

} 