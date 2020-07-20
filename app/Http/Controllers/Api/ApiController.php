<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * 返回参数
     * $code 状态码 $msg 返回信息 $data 返回数据
     */
    public function returnJson($code, $msg, $data = [])
    {
        return response()->json([
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data
        ]);
    }
}