<?php
namespace Zidisha\ScheduledJob;

use Illuminate\Queue\Jobs\Job;
use Zidisha\ScheduledJob\Base\ScheduledJob as BaseScheduledJob;

abstract class ScheduledJob extends BaseScheduledJob
{
    const COUNT = 1;

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    abstract public function getQuery();
    
    abstract public function process(Job $job);
}
