<?php

use Illuminate\Console\Command;
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
                /** @var ScheduledJob $scheduledJob */
                if ($job->scheduled_job_id == null) {
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
        }
    }

    /**
     * @param ScheduledJob $scheduledJobClass
     * @return \Illuminate\Database\Query\Builder
     */
    public function joinQuery($scheduledJobClass)
    {
        $query = $scheduledJobClass->getQuery()
            ->addSelect('s.id as scheduled_job_id')
            ->leftJoin('scheduled_jobs AS s', function($join) {
                $join
                    ->on('user_id', '=', 's.user_id')
                    ->on('start_date' , '=', 's.start_date');
            })
            ->whereRaw('s.count < '.$scheduledJobClass::COUNT);
        
        if ($scheduledJobClass::COUNT > 1) {
            $query->whereRaw("s.last_processed_at IS NULL OR (s.last_processed_at + (s.count || ' months')::interval) > NOW()");
        } else {
            $query->whereRaw("s.last_processed_at IS NULL");
        }
        
        return $query;
    }
}
