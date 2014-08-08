<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\ScheduledJobLog;
use Zidisha\Vendor\PropelDB;

class EnqueueScheduledJobs extends Command
{

    protected $name = 'EnqueueScheduledJobs';

    protected $description = 'Enqueue the jobs to be executed.';

    public function fire()
    {
        $jobsQuery = DB::raw(
            'SELECT
                scheduled_jobs.id as job_id, scheduled_jobs.count as job_count
             FROM
                 scheduled_jobs
             LEFT JOIN scheduled_jobs_logs
               ON scheduled_jobs.id = scheduled_jobs_logs.scheduled_jobs_id
              AND scheduled_jobs.count = scheduled_jobs_logs.count
             WHERE
                scheduled_jobs_logs.id IS NULL'
        );

        $jobs = DB::select($jobsQuery);

        foreach ($jobs as $job) {
            $jobId = $job->job_id;
            $jobCount = $job->job_count;

            $jobLog = new ScheduledJobLog();
            $jobLog->setScheduledJobsId($jobId);
            $jobLog->setCount($jobCount);
            $jobLog->save();

            \Queue::push('Zidisha\ScheduledJob\ScheduledJobService@handleScheduledJob', ['jobLogId' => $jobLog->getId()]);
        }
    }
}
