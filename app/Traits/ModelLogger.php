<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/6
 * Time: 15:11
 */

namespace App\Traits;


use App\ModelLog;
use App\Jobs\ModelLoggerJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

trait ModelLogger
{
    protected static function getLoggerEvents()
    {
        //   'retrieved'
        if (isset(static::$loggerEvents) && is_array(static::$loggerEvents)) {
            return static::$loggerEvents;
        }
        return [
            'created', 'updated', 'deleted', 'restored', 'forceDeleted',
        ];
    }

    public static function bootModelLogger()
    {
        foreach (static::getLoggerEvents() as $event) {
            if (method_exists(static::class, $event)) {
                static::$event(function ($model) use ($event) {
                    if (!app()->runningInConsole()) {
                        static::logger($model, $event);
                    }
                });
            }
        }
    }


    protected static function logger(Model $model, $event)
    {
        $request = request();

        if (!app()->runningInConsole()) {
            $need_replace = ['password', 'admin_password'];

            $route_information = app()->runningInConsole() ? ['cli'] : get_route_information(Route::current());

            $request_params = json_encode(replace_if_exist_in_array($request->input(), $need_replace), JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);

            $data = [
                'model' => get_class($model),
                'model_id' => $model->{$model->primaryKey},
                'model_event' => $event,
                'user_id' => optional($request->user())->id,
                'route_information' => json_encode($route_information, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE),
                'controller_and_action' => Route::currentRouteAction(),
                'request_params' => $request_params
            ];

            dispatch(new ModelLoggerJob($data));
        }


    }
}