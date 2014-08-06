<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Zidisha\Mail\LenderMailer;
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
class NewLenderIntro extends ScheduledJobs
{

    /**
     * Constructs a new NewLenderIntro class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_9.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_9);
    }

    public function getQuery()
    {
        return DB::table('users AS u')
            ->selectRaw('u.id AS user_id, u.created_at as start_date, *')
            ->whereRaw('u.role = 0')
            ->whereRaw('u.active = true')
            ->whereRaw("u.created_at  >'".Carbon::now()->subDays(2)."'")
            ->whereRaw("u.created_at <='".Carbon::now()->subDay()."'");
    }

    public function process($job, $data)
    {
        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($data['jobId']);

        $lender = $scheduleJobs->getUser()->getLender();

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
        $lenderMailer->sendIntroductionMail($lender);
    }
} // NewLenderIntro
