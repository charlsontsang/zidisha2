<?php
namespace Zidisha\ScheduledJob;

use Illuminate\Database\Query\Builder;
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

    public function joinQuery(Builder $query, $userIdColumn, $startDateColumn)
    {
        $query
            ->selectRaw("$userIdColumn AS user_id, $startDateColumn AS start_date")
            ->addSelect('s.id as scheduled_job_id')
            ->leftJoin('scheduled_jobs AS s', function($join) use ($userIdColumn, $startDateColumn) {
                    $join
                        ->on($userIdColumn, '=', 's.user_id')
                        ->on($startDateColumn , '=', 's.start_date')
                        ->where('s.class_key', '=', $this->getClassKey());
                });

        if ($this::COUNT >= 1) {
            $query->whereRaw("s.id IS NULL OR (s.last_processed_at IS NOT NULL AND (s.created_at + (s.count || ' month')::interval) < NOW() AND s.count <= ".$this::COUNT . ")");
        } else {
            $query->whereRaw("s.id IS NULL");
        }
        return $query;
    }
}
