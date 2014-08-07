<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\AbandonedUser;
use Zidisha\ScheduledJob\ScheduledJob;
use Zidisha\ScheduledJob\ScheduledJobQuery;

class ScheduledJobs extends Command
{

    protected $name = 'ScheduledJobs';

    protected $description = 'This command is to scheduled cron jobs';

    protected $classes = [
        'Zidisha\ScheduledJob\AbandonedUser',
        'Zidisha\ScheduledJob\LoanAboutToExpireReminder',
        'Zidisha\ScheduledJob\LoanFinalArrear',
        'Zidisha\ScheduledJob\AgainRepaymentReminder',
        'Zidisha\ScheduledJob\LoanFirstArrear',
        'Zidisha\ScheduledJob\RepaymentReminder',
        'Zidisha\ScheduledJob\MonthlyLoanArrear',
        'Zidisha\ScheduledJob\NewLenderIntro',
//        'Zidisha\ScheduledJob\CronToRepay',
//        'Zidisha\ScheduledJob\UnusedFunds',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        foreach ($this->classes as $class) {
            $scheduledJobClass = \App::make($class);
            $query = $this->joinQuery($scheduledJobClass);
            $jobs = $query->get();
            
            foreach ($jobs as $job) {
                if ($job->schedule_job_id == null) {
                    $scheduledJob = new ScheduledJob();
                    $scheduledJob->setUserId($job->user_id);
                    $scheduledJob->SetClassKey($scheduledJobClass->getClassKey());
                    $scheduledJob->save();
                } else {
                    $scheduledJob = ScheduledJobQuery::create()
                        ->findOneById($job->schedule_job_id);
                    
                    $scheduledJob->setCount($scheduledJob->getCount() + 1);
                    $scheduledJob->save();
                }
            }
        }
    }

    public function joinQuery($scheduledJobClass)
    {
        $query = $scheduledJobClass->getQuery()
            ->addSelect('s.id as schedule_job_id')
            ->leftJoin('scheduled_jobs AS s', 'user_id', '=', 's.user_id')
            ->whereRaw('s.start_date = start_date')
            ->whereRaw('s.user_id = user_id')
            ->whereRaw('s.count < '.$scheduledJobClass::COUNT);
        
            if ($scheduledJobClass::COUNT > 0) {
                return $query->whereRaw("s.last_processed_at IS NULL OR (s.last_processed_at + (s.count || ' months')::interval) > NOW()");
            } else {
                return $query->whereRaw("s.last_processed_at IS NULL");
            }
    }
}
