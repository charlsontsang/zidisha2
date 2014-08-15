<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;

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
class AbandonedUser extends ScheduledJob
{
    const COUNT = 3;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;
    /**
     * @var ScheduledJobService
     */
    private $scheduledJobService;

    /**
     * Constructs a new AbandonedUser class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_1.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_1);
    }

    public function getQuery()
    {
        return DB::table('users AS u')
            ->selectRaw('u.id AS user_id, u.last_login_at as start_date')
            ->whereRaw("u.last_login_at < '".Carbon::now()->subYear()."'")
            ->whereRaw('u.role = 0')
            ->whereRaw('u.active = true');
    }
    
    public function process(Job $job)
    {
        $user = $this->getUser();

        if ($user->getLastLoginAt() < Carbon::create()->subYear()) {
            /** @var  LenderMailer $lenderMailer */
            $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
            $lenderMailer->sendAbandonedUserMail($user);

        }
        
        $job->delete();
    }
} // AbandonedUser
