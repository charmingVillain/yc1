<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/24
 * Time: 15:35
 */

namespace App;


use Illuminate\Http\JsonResponse;

class SimpleResponse extends JsonResponse
{
    /**
     * @param $message
     * @param $status
     * @param int $status_code
     * @param null $data
     * @param null $errors
     * @return SimpleResponse
     */
    public static function createFromMessage($message, $status, $status_code = 200, $data = null, $errors = null)
    {
        $static = new static();
        if ($errors) {
            $static->setData(compact(['message', 'status', 'data', 'errors']));
        } else {
            $static->setData(compact(['message', 'status', 'data']));
        }
        $static->setStatusCode($status_code);
        return $static;
    }

    /**
     * 成功的响应
     * @param $message
     * @param int $status
     * @param string|array $data
     * @return SimpleResponse
     */
    public static function success($message, $data = null, $status = 200)
    {
        return static::createFromMessage($message, $status, 200, $data);
    }

    /**
     * @param $message
     * @param int $status
     * @param int $status_code
     * @param null $data
     * @param null $errors
     * @return SimpleResponse
     */
    public static function error($message, $status = 520, $status_code = 520, $data = null, $errors = null)
    {
        return static::createFromMessage($message, $status, $status_code, $data, $errors);
    }


}
