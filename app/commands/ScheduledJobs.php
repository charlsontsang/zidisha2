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
            ->whereRaw('s.start_date = u.last_login_at')
            ->whereRaw('s.user_id = u.id')
            ->whereRaw("s.last_processed_at IS NULL OR (s.last_processed_at + (s.count || ' months')::interval) > NOW()")
            ->whereRaw("
                    ( 
                        (
                            s.last_processed_at + (s.COUNT || ' months') :: INTERVAL
                        ) > NOW() OR  
                        (
                            s.last_processed_at + (s.COUNT || ' months') :: INTERVAL
                        ) IS NULL
                    )
                ");
        
//        dd($query->toSql());
        $users = $query->get();
        
        foreach ($users as $user) {

            var_dump($user);
        }

    }
}
