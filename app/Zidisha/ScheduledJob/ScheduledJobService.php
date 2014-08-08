<?php
namespace Zidisha\ScheduledJob;

use Illuminate\Queue\Jobs\Job;
use Zidisha\Vendor\PropelDB;

class ScheduledJobService
{

    public function handleScheduledJob(Job $job, array $data)
    {
        $scheduleJobLog = ScheduledJobLogQuery::create()
            ->findOneById($data['scheduledJobLogId']);

        $scheduledJob = $scheduleJobLog->getScheduledJob();
        $scheduledJob->process($job, $data);

        if ($job->isDeleted()) {
            PropelDB::transaction(
                function ($con) use ($scheduledJob, $scheduleJobLog) {
                    $now = new \DateTime;

                    $scheduledJob->setLastProcessedAt($now);
                    $scheduledJob->save($con);

                    $scheduleJobLog->setProcessedAt($now);
                    $scheduleJobLog->save($con);
                }
            );
        }
    }
} 
