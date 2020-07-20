<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/6
 * Time: 15:11
 */

namespace App\Traits;


use App\Exceptions\NotificationException;
use App\ModelLog;
use App\Jobs\ModelLoggerJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait CreateUniqueNo
{

    /**
     * 保存本程序已经使用了的编号
     * @var array
     */
    protected static $create_unique_no_list = [];

    protected static function getCreateNoEvents()
    {
        return [
            'creating'
        ];
    }

    public static function bootCreateUniqueNo()
    {
        foreach (static::getCreateNoEvents() as $event) {
            if (method_exists(static::class, $event)) {
                static::$event(function ($model) use ($event) {
                    $create_unique_no_prefix = static::$create_unique_no_prefix ?? 'N';
                    $create_unique_no_field = static::$create_unique_no_field;
                    if (empty($create_unique_no_field)) {
                        throw new NotificationException('请先设置唯一字段');
                    }

                    $model->{static::$create_unique_no_field} = static::createUniqueNoFunc($create_unique_no_prefix);
                });
            }
        }
    }

    public static function createUniqueNoFunc($prefix = 'N', $try = 1)
    {
        $create_unique_no_field = static::$create_unique_no_field;
        if (empty($create_unique_no_field)) {
            throw new NotificationException('请先设置唯一字段');
        }
        $time_str = Carbon::now()->format('YmdHis');
        $max = 99999;
        $first = static::where($create_unique_no_field, 'like', "$prefix$time_str%")->orderBy('id', 'desc')->first(['id', $create_unique_no_field]);
        $next = 1;
        if ($first) {
            $next = intval(Str::after($first->{$create_unique_no_field}, $prefix . $time_str)) + 1;
        }

        $no = $prefix . $time_str . sprintf('%05s', $next);

        if (in_array($no, static::$create_unique_no_list)) {
            $max = collect(static::$create_unique_no_list)->map(function ($item) use ($prefix, $time_str) {
                return intval(Str::after($item, $prefix . $time_str));
            })->max();
            $next = $max + 1;
        }

        // $next 不能大于99999
        if ($next > $max) {
            sleep(1);
            if ($try > 10) {
                throw new NotificationException('生成唯一编号重试次数超过');
            }
            return static::createUniqueNoFunc($prefix, $try + 1);
        }

        $no = $prefix . $time_str . sprintf('%05s', $next);

        array_push(static::$create_unique_no_list, $no);
        return $no;
    }
}