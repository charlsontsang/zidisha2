<?php
namespace Zidisha\ScheduledJob;

use Zidisha\ScheduledJob\Base\ScheduledJobs as BaseScheduledJobs;

class ScheduledJobs extends BaseScheduledJobs
{
    /**
     * @var ScheduledJobService
     */
    private $scheduledJobService;

    public function fire($job, $data)
    {
        $jobsId = $data['jobId'];
        $jobCount = $data['jobCount'];

        $scheduleJobs = ScheduledJobsQuery::create()
            ->findOneById($jobsId);

        $scheduleJobs->process($job, $data);

        $job->delete();

        if ($job->isDeleted()) {
            /** @var  ScheduledJobService $scheduleJobService */
            $scheduleJobService = \App::make('Zidisha\ScheduledJob\ScheduledJobService');
            $scheduleJobService->updateJob($scheduleJobs, $jobsId, $jobCount);
        }
    }

    public function process($job, $data)
    {        
        exit;
    }
}
