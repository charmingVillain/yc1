<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/5/24
 * Time: 15:34
 */

namespace App\Traits;


use App\SimpleResponse as Response;

trait SimpleResponse
{
    /**
     * 成功
     * @param $message
     * @return Response
     */
    public function success($message,$status = 200)
    {
        return Response::success($message,$status);
    }

    /**
     * 失败
     * @param $message
     * @return Response
     */
    public function error($message)
    {
        return Response::error($message);
    }

    /**
     * 其他响应
     * @param $message
     * @param int $status
     * @param int $status_code
     * @return Response
     */
    public function createFromMessage($message, int $status, $status_code = 200)
    {
        return Response::createFromMessage($message, $status, $status_code);
    }
}
