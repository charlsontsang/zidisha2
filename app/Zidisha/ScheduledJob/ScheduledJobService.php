<?php
namespace Zidisha\ScheduledJob;

use Zidisha\Vendor\PropelDB;

class ScheduledJobService
{
    public function handleScheduledJob($job, $data)
    {
        $jobLogId = $data['jobLogId'];

        $scheduleJobLog = ScheduledJobLogQuery::create()
            ->findOneById($jobLogId);

        $scheduleJob = $scheduleJobLog->getScheduledJobs();
        $scheduleJob->process($job, $data);

        if ($job->isDeleted()) {
            PropelDB::transaction(
                function ($con) use ($scheduleJob, $scheduleJobLog) {
                    $now = new \DateTime;

                    $scheduleJob->setLastProcessedAt($now);
                    $scheduleJob->save($con);

                    $scheduleJobLog->setProcessedAt($now);
                    $scheduleJobLog->save($con);
                }
            );
        }
        
    }
} 
