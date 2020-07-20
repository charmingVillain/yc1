<?php

namespace App\Jobs;

use App\CacheKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CacheForgetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $limit = 500;

    protected $start = 1;

    protected $group = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($group = null, $start = 1)
    {
        $this->group = $group;
        $this->start = $start;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $query = CacheKey::with([])
            ->where('id', '>=', $this->start)
            ->limit($this->limit)
            ->orderBy('id', 'asc');

        if ($this->group) {
            $query->where('group', '=', $this->group);
        }

        $items = $query->get();

        $items->each(function (CacheKey $item) {
            Cache::forget(data_get($item, 'key'));
            $item->delete();
        });

        if ($items->count() == $this->limit) {
            $last = $items->last();
            $last_id = data_get($last, 'id');
            if ($last_id) {
                $job = new CacheForgetJob($this->group, $last_id + 1);
                if (config('app.env') == 'production') {
                    dispatch($job);
                } else {
                    dispatch_now($job);
                }
            }
        }
    }

}
