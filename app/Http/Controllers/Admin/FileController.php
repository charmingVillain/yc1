<?php

namespace App\Http\Controllers\Admin;

use App\File;
use App\Goods;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{

    /**
     * @return File
     */
    public function getModel()
    {
        return new File;
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @throws \App\Exceptions\NotificationException
     */
    public function image(Request $request)
    {
        return (new File)->image($request);
    }

    /**
     * 获取图片信息
     * @param Request $request
     * @return mixed
     */
    public function getFileInfoById(Request $request)
    {
        $id = $request->input('id');
        if (!is_array($id)) {
            $id = [$id];
        }
        return $this->getModel()->find($id);
    }

    public function test()
    {
        return view('admin.file.test');
    }
}
