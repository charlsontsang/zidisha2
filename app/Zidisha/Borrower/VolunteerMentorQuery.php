<?php

namespace Zidisha\Borrower;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Borrower\Base\VolunteerMentorQuery as BaseVolunteerMentorQuery;
use Zidisha\Country\CountryQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'volunteer_mentor' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class VolunteerMentorQuery extends BaseVolunteerMentorQuery
{

    public function getVolunteerMentorCity()
    {
        $countryCode = \Session::get('BorrowerJoin.countryCode');
        $country = CountryQuery::create()
            ->filterByCountryCode($countryCode)
            ->findOne();

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $sql = "SELECT DISTINCT city FROM borrower_profiles WHERE borrower_id IN "
            . "(SELECT borrower_id FROM volunteer_mentor WHERE country_id = :country_id AND status = :status
            AND mentee_count < :mentee_count)";
        $stmt = $con->prepare($sql);
        //TODO to make mentee_count = 50
        $stmt->execute(array(':country_id' => $country->getId(), ':status' => '1', ':mentee_count' => '25'));
        $cities = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return array_combine($cities, $cities);
    }

    public function getVolunteerMentorByCity($city)
    {
        $list = [];
        $volunteerMentors = VolunteerMentorQuery::create()
            ->filterByActive(true)
            ->filterByMenteeCount(array('max' => '25'))
            ->useBorrowerVolunteerQuery()
            ->useProfileQuery()
            ->filterByCity($city)
            ->endUse()
            ->endUse()
            ->joinWith('VolunteerMentor.BorrowerVolunteer')
            ->find();

        foreach ($volunteerMentors as $volunteerMentor) {
            $list[$volunteerMentor->getBorrowerId()] = $volunteerMentor->getBorrowerVolunteer()->getName();
        }

        return $list;
    }

} // VolunteerMentorQuery
