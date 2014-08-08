<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\AbandonedUser;

class ScheduledJobs extends Command
{

    protected $name = 'scheduled-jobs';

    protected $description = 'This command is to run scheduled cron jobs';

    protected $classes = [
        'Zidisha\ScheduledJob\AbandonedUser',
        'Zidisha\ScheduledJob\LoanAboutToExpireReminder',
        'Zidisha\ScheduledJob\LoanFinalArrear',
        'Zidisha\ScheduledJob\AgainRepaymentReminder',
        'Zidisha\ScheduledJob\LoanFirstArrear',
        'Zidisha\ScheduledJob\RepaymentReminder',
        'Zidisha\ScheduledJob\MonthlyLoanArrear',
        'Zidisha\ScheduledJob\NewLenderIntro',
        'Zidisha\ScheduledJob\CronToRepay',
        'Zidisha\ScheduledJob\UnusedFunds',
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
//             print_r($query->toSql());
//            dd();
            $jobs = $query->get();
            foreach ($jobs as $job) {
                if ($job->schedule_job_id == null) {
                    $scheduledJob = new $class;
                    $scheduledJob->setUserId($job->user_id);
                    $scheduledJob->save();
                } else {
                    $scheduledJob = ScheduledJobQuery::create()
                        ->findOneById($job->scheduled_job_id);
                    
                    $scheduledJob->setCount($scheduledJob->getCount() + 1);
                    $scheduledJob->save();
                }
            }
                dd();
        }
    }

    public function joinQuery($scheduledJobClass)
    {
       $query = $scheduledJobClass->getQuery()
            ->leftJoin('scheduled_jobs AS s', 'user_id', '=', 's.user_id')
            ->whereRaw('s.start_date = start_date')
            ->whereRaw('s.user_id = user_id')
            ->addSelect('s.id as scheduled_job_id')
            ->whereRaw('s.count < '.$scheduledJobClass::COUNT);
        
            if ($scheduledJobClass::COUNT > 0) {
                return $query->whereRaw("s.last_processed_at IS NULL OR (s.last_processed_at + (s.count || ' months')::interval) > NOW()");
            } else {
                return $query->whereRaw("s.last_processed_at IS NULL");
            }
    }
}
