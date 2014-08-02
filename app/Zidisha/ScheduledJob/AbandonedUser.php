<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Base\ScheduledJobsQuery;
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
class AbandonedUser extends ScheduledJobs
{
    const PERIOD = 1;
    const COUNT = 0;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;

    /**
     * Constructs a new AbandonedUser class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_1.
     */
    public function __construct(LenderMailer $lenderMailer)
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobsTableMap::CLASSKEY_1);
        $this->lenderMailer = $lenderMailer;
    }

    public function getQuery()
    {
        return DB::table('users AS u')
            ->whereRaw('u.last_login_at < '.Carbon::now()->subYear())
            ->whereRaw('u.role = 0')
            ->whereRaw('u.active = true');
    }

    public function fire($job, $data)
    {
        $jobsId = $data['jobsId'];

        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($jobsId);

        $user = $scheduleJobs->getUser();
        
        $this->lenderMailer->sendAbandonedUserMail($user);
        
        $job->delete();

        if ($job->isDeleted()) {

            $now = new \DateTime;

            $scheduleJobs->setLastProcessedAt($now);
            $scheduleJobs->save();

            $scheduleJobsLogs = ScheduledJobsLogsQuery::create()
                ->findOneByScheduledJobsId($jobsId);

            $scheduleJobsLogs->setCount($scheduleJobsLogs->getCount() + 1);
            $scheduleJobsLogs->setProcessedAt($now);
            $scheduleJobsLogs->save();
        }
    }
} // AbandonedUser
