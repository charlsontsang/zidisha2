<?php

namespace Zidisha\ScheduledJob;

use Zidisha\Mail\BorrowerMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobsTableMap;


/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'scheduled_jobs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LoanFirstArrear extends ScheduledJobs
{

    /**
     * Constructs a new LoanFirstArrear class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_2.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_2);
    }

    public function getQuery()
    {
        return DB::table('users AS u');
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $user = $scheduleJobs->getUser();

        /** @var  BorrowerMailer $borrowerMailer */
        $borrowerMailer = \App::make('Zidisha\Mail\BorrowerMailer');
        $borrowerMailer->sendLoanFirstArrear($user);
    }
} // LoanFirstArrear
