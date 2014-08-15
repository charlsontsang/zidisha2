<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\AbandonedUser;
use Zidisha\ScheduledJob\ScheduledJob;
use Zidisha\ScheduledJob\ScheduledJobQuery;

class ScheduledJobs extends Command
{

    protected $name = 'scheduled-jobs';

    protected $description = 'This command is to run scheduled cron jobs';

    protected $classes = [
//        'Zidisha\ScheduledJob\AbandonedUser',
//        'Zidisha\ScheduledJob\LoanAboutToExpireReminder',
//        'Zidisha\ScheduledJob\LoanFinalArrear',
//        'Zidisha\ScheduledJob\AgainRepaymentReminder',
//        'Zidisha\ScheduledJob\LoanFirstArrear',
        'Zidisha\ScheduledJob\RepaymentReminder',
        'Zidisha\ScheduledJob\MonthlyLoanArrear',
        'Zidisha\ScheduledJob\NewLenderIntro',
        'Zidisha\ScheduledJob\CronToRepay',
        'Zidisha\ScheduledJob\UnusedFunds',
//        'Zidisha\ScheduledJob\MonthlyLoanArrear',
//        'Zidisha\ScheduledJob\NewLenderIntro',
//        'Zidisha\ScheduledJob\CronToRepay',
//        'Zidisha\ScheduledJob\UnusedFunds',
    ];
//        'Zidisha\ScheduledJob\MonthlyLoanArrear',
//        'Zidisha\ScheduledJob\CronToRepay',
    
    protected $classesWithLoan = [
        'Zidisha\ScheduledJob\AgainRepaymentReminder',
        'Zidisha\ScheduledJob\CronToRepay',
        'Zidisha\ScheduledJob\LoanFinalArrear',
        'Zidisha\ScheduledJob\LoanFirstArrear',
        'Zidisha\ScheduledJob\MonthlyLoanArrear',
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
//            dd($scheduledJobClass->getClassKey());
            $query = $this->joinQuery($scheduledJobClass);

//             print_r($query->toSql());
//            dd();
            $jobs = $query->get();
//            print_r($query ->toSql());
//            dd();
            $jobs = $query->get();

            foreach ($jobs as $job) {
                /** @var ScheduledJob $scheduledJob */
                if ($job->scheduled_job_id == null) {
                    $scheduledJob = new $class;
                    $scheduledJob->setUserId($job->user_id);
                    $scheduledJob->setStartDate($job->start_date);
                    if (in_array($class, $this->classesWithLoan)) {
                        $scheduledJob->setLoanId($job->loan_id);
                    }
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

    /**
     * @param ScheduledJob $scheduledJobClass
     * @return \Illuminate\Database\Query\Builder
     */
    public function joinQuery($scheduledJobClass)
    {
        $query = $scheduledJobClass->getQuery()
            ->addSelect('s.id as scheduled_job_id')
            ->leftJoin('scheduled_jobs AS s', function($join) use ($scheduledJobClass) {
                $join
                    ->on('user_id', '=', 's.user_id')
                    ->on('start_date' , '=', 's.start_date')
                    ->where('s.class_key', '=', $scheduledJobClass->getClassKey());
            });

        if ($scheduledJobClass::COUNT > 1) {
            $query->whereRaw("s.id IS NULL OR (s.last_processed_at IS NOT NULL AND (s.created_at + '1 month'::interval) < NOW() AND s.count < ".$scheduledJobClass::COUNT . ")");
        } else {
            $query->whereRaw("s.id IS NULL");
        }
        
        return $query;
    }
}
