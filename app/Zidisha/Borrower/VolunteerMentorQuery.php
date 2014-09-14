<?php

namespace Zidisha\Borrower;

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Borrower\Base\VolunteerMentorQuery as BaseVolunteerMentorQuery;
use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;
use Zidisha\Vendor\PropelDB;


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

    public function getVolunteerMentorCities(Country $country)
    {
        // TODO fix mentee_count?
        $sql = "SELECT DISTINCT city FROM borrower_profiles WHERE borrower_id IN "
            . "(SELECT borrower_id FROM volunteer_mentors WHERE country_id = :country_id AND active = true
            AND mentee_count < :mentee_count)";
        $_cities = PropelDB::fetchAll($sql, [':country_id' => $country->getId(), ':mentee_count' => '25']);

        $cities = [];
        foreach ($_cities as $city) {
            $cities[$city['city']] = $city['city'];
        }
        
        return $cities;
    }

    public function getVolunteerMentorsByCity($city)
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
