<?php
namespace Zidisha\ScheduledJob;

use Zidisha\Vendor\PropelDB;

class ScheduledJobService
{
    public function handleScheduledJob($job, $data)
    {
        $scheduleJobLog = ScheduledJobLogQuery::create()
            ->findOneById($data['scheduledJobLogId']);

        $scheduledJob = $scheduleJobLog->getScheduledJobs();
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
