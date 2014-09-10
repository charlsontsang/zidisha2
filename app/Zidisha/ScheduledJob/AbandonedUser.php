<?php

namespace Zidisha\ScheduledJob;

use Carbon\Carbon;
use DB;
use Illuminate\Queue\Jobs\Job;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\Mail\LenderMailer;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;
use Zidisha\User\User;

/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'scheduled_jobs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class AbandonedUser extends ScheduledJob
{
    const COUNT = 1;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;
    /**
     * @var ScheduledJobService
     */
    private $scheduledJobService;

    /**
     * Constructs a new AbandonedUser class, setting the class_key column to ScheduledJobsTableMap::CLASSKEY_1.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_1);
    }

    public function getQuery()
    {
        $query = DB::table('users AS u')
            ->whereRaw("u.last_login_at < '" . Carbon::now()->subYear() . "'")
            ->whereRaw('u.role = ' . User::LENDER_ROLE_ENUM)
            ->whereRaw('u.active = true');

        return $this->joinQuery($query, 'u.id', 'u.last_login_at');
    }

    public function process(Job $job)
    {
        $user = $this->getUser();

        if ($user->getLastLoginAt() < Carbon::now()->subYear() && $this->getCount() == 1) {
            /** @var  LenderMailer $lenderMailer */
            $lenderMailer = \App::make('Zidisha\Mail\LenderMailer');
            $lenderMailer->sendAbandonedUserMail($user);
        } elseif ($user->getLastLoginAt() < Carbon::now()->subYear()->subMonth() && $this->getCount() == 2) {
            $lender = LenderQuery::create()
                ->findOneById($user->getId());
            /** @var LenderService $lenderService */
            $lenderService = \App::make('\Zidisha\Lender\LenderService');
            $lenderService->deactivateLender($lender);
        }

        $job->delete();
    }
} // AbandonedUser
