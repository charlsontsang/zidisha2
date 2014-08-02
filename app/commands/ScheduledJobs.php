<?php

use Illuminate\Console\Command;
use Zidisha\ScheduledJob\AbandonedUser;

class ScheduledJobs extends Command
{

    protected $name = 'scheduled-jobs';

    protected $description = 'This command is to run scheduled cron jobs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        /** @var  Zidisha\ScheduledJob\AbandonedUser $abandonedUser */
        $abandonedUser = \App::make('Zidisha\ScheduledJob\AbandonedUser');
        $query = $abandonedUser->getQuery()
            ->leftJoin('scheduled_jobs AS s', 'u.id', '=', 's.user_id')
            ->whereRaw('s.type = 0')
            ->whereRaw('s.start_date = u.last_login_at')
            ->whereRaw('s.user_id = u.id');
        $query->whereRaw("s.last_processed_at IS NULL OR (s.last_processed_at + (s.count || ' months')::interval) > NOW()");
        
        dd($query->toSql());
    }
}
