<?php

namespace Zidisha\ScheduledJob;

use Illuminate\Queue\Jobs\Job;
use Zidisha\ScheduledJob\Map\ScheduledJobTableMap;


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
class AutomatedLending extends ScheduledJob
{

    /**
     * Constructs a new AutomatedLending class, setting the class_key column to ScheduledJobTableMap::CLASSKEY_11.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(ScheduledJobTableMap::CLASSKEY_11);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery()
    {
    }

    public function process(Job $job)
    {
    }
} // AutomatedLending
