<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class HorizonFlush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' Flush all of the horizon failed queue jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Redis::connection()->del('horizon:failed:*');
        Redis::connection()->del('horizon:failed_jobs');
        $this->info('success');
    }
}
