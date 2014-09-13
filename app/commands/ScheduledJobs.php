<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\AbandonedUser;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\ScheduledJob\ScheduledJob;
use Zidisha\ScheduledJob\ScheduledJobQuery;

class ScheduledJobs extends Command
{

    protected $name = 'ScheduledJobs';

    protected $description = 'This command is to run scheduled cron jobs';

    protected $classes = [
//        'Zidisha\ScheduledJob\AbandonedUser',
//        'Zidisha\ScheduledJob\LoanAboutToExpireReminder',
//        'Zidisha\ScheduledJob\LoanFinalArrear',
//        'Zidisha\ScheduledJob\AgainRepaymentReminder',
//        'Zidisha\ScheduledJob\LoanFirstArrear',
//        'Zidisha\ScheduledJob\RepaymentReminder',
//        'Zidisha\ScheduledJob\MonthlyLoanArrear',
//        'Zidisha\ScheduledJob\NewLenderIntro',
//        'Zidisha\ScheduledJob\CronToRepay',
//        'Zidisha\ScheduledJob\UnusedFunds',
        'Zidisha\ScheduledJob\InviteeOwnFunds',
    ];
    
    protected $classesWithLoan = [
        'Zidisha\ScheduledJob\AgainRepaymentReminder',
        'Zidisha\ScheduledJob\RepaymentReminder',
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
            $jobs = $scheduledJobClass->getQuery()->get();

            foreach ($jobs as $job) {
                /** @var ScheduledJob $scheduledJob */
                if ($job->scheduled_job_id == null) {
                    $scheduledJob = new $class;
                    $scheduledJob->setUserId($job->user_id)
                        ->setStartDate($job->start_date);
                    if (in_array($class, $this->classesWithLoan)) {
                        $scheduledJob->setLoanId($job->loan_id);
                    }
                } else {
                    $scheduledJob = ScheduledJobQuery::create()
                        ->findOneById($job->scheduled_job_id);
                }
                $scheduledJob->setCount($scheduledJob->getCount() + 1);
                $scheduledJob->save();
            }
        }
    }
}
