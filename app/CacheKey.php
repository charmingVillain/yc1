<?php

namespace App;

use App\Jobs\CacheClearOneJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheKey extends Model
{


    // ali_sts_token 阿里大文件授权

    public $timestamps = false;

    protected $fillable = ['end_at', 'key', 'group'];

    protected $dates = [
        'end_at'
    ];

    /**
     * 保存键值，并作其他操作
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function store($data)
    {
        $create = self::with([])->create($data);
        // 建立一个延迟队列删除过期的key
        if (config('app.env') == 'production' && $data['end_at'] instanceof Carbon) {
            dispatch(new CacheClearOneJob($create))->delay($data['end_at']);
        }
        return $create;
    }


    /**
     * 清空缓存
     */
    public function flush()
    {
        Cache::flush();
    }
}
