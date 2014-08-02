<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\ScheduledJobsLogs;
use Zidisha\ScheduledJob\ScheduledJobsQuery;

class Process extends Command
{

    protected $name = 'process';

    protected $description = 'Process the cron jobs.';

    public function fire()
    {
        $jobsQuery = DB::raw(
            '
                    SELECT
                        scheduled_jobs.id as job_id, scheduled_jobs.count as job_count, *
                    FROM
                        scheduled_jobs
                    LEFT JOIN scheduled_jobs_logs ON scheduled_jobs.id = scheduled_jobs_logs.scheduled_jobs_id
                    AND scheduled_jobs.count = scheduled_jobs_logs.count
                    WHERE
                        scheduled_jobs_logs. ID IS NULL
                '
        );

        $jobs = DB::select($jobsQuery);
        
        foreach ($jobs as $job) {
            dd($job);
            $jobId = $job->job_id;
            $jobCount = $job->job_count;

            \Queue::push('Zidisha\ScheduledJob\AbandonedUser', compact('jobsId'));
            
            $jobCount += 1;
            
            $scheduleJob = ScheduledJobsQuery::create()
                ->findOneById($jobId);
            
            $scheduleJob->setCount($jobCount);
            $scheduleJob->save();
            

            $jobsLog = new ScheduledJobsLogs();
            $jobsLog->setScheduledJobsId($jobId);
            $jobsLog->setCount($jobCount);
            $jobsLog->save();
        }
    }
}
