<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\User\User;


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
class NewLenderIntro extends ScheduledJob
{

    /**
     * Constructs a new NewLenderIntro class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_9.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_9);
    }

    public function getQuery()
    {
        $query =  DB::table('users AS u')
            ->whereRaw('u.role = ' . User::LENDER_ROLE_ENUM)
            ->whereRaw('u.active = true')
            ->whereRaw("u.created_at  >'" . Carbon::now()->subDays(2) . "'")
            ->whereRaw("u.created_at <='" . Carbon::now()->subDay() . "'");

        return $this->joinQuery($query, 'u.id', 'u.last_login_at');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();
        $lender = $user->getLender();

        /** @var  LenderMailer $lenderMailer */
        $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
        $lenderMailer->sendIntroductionMail($lender);

        $job->delete();
    }
} // NewLenderIntro
