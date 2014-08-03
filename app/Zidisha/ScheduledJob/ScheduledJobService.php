<?php
namespace Zidisha\ScheduledJob;

class ScheduledJobService
{
    /**
     * @param $scheduleJobs
     * @param $jobId
     * @param $jobCount
     */
    public function updateJob(ScheduledJobs $scheduleJobs, $jobId, $jobCount)
    {
        $now = new \DateTime;

        $scheduleJobs->setLastProcessedAt($now);
        $scheduleJobs->save();

        $scheduleJobsLogs = ScheduledJobsLogsQuery::create()
            ->filterByScheduledJobsId($jobId)
            ->filterByCount($jobCount)
            ->findOne();

        $scheduleJobsLogs->setProcessedAt($now);
        $scheduleJobsLogs->save();
    }
} 
