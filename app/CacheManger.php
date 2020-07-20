<?php


namespace App;


use App\Jobs\CacheForgetJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheManger
{
    /**
     * 清楚后台菜单缓存
     */
    public static function clearAdminMenu()
    {
        $job = new CacheForgetJob('admin_menu');
        if (config('app.env') == 'production') {
            dispatch($job);
        } else {
            dispatch_now($job);
        }
    }

}
