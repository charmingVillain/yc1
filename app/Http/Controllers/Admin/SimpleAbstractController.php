<?php
/**
 * Created by PhpStorm.
 * User: 56301
 * Date: 2020/2/27
 * Time: 10:43
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class SimpleAbstractController extends Controller
{

    /**
     * 获取操作模型
     * @return Model
     */
    abstract protected function getModel();


    /**
     * 实现搜索
     * @param Request $request
     * @return Builder
     */
    abstract protected function search(Request $request): Builder;


    /**
     * 文本文档
     * @return View
     */
    public function view(): View
    {
        // 根据类名猜测文件名
        $class = get_called_class();

        $class_name = Str::after($class, 'App\Http\Controllers\Admin\\');

        $class_name = str_replace('Controller', '', $class_name);

        $view_name = Str::snake($class_name);

        return view("admin.{$view_name}.index");
    }


    /**
     * 列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|View
     */
    public function index()
    {
        $request = request();
        if ($request->ajax()) {
            $model = $this->search($request);
            if ($request->header('page-simple') == 'true') {
                return $model->simplePaginate($request->input('pageSize'));
            } else {
                return $model->paginate($request->input('pageSize'));
            }
        } else {
            return $this->view();
        }
    }


    /**
     * 获取详情
     * @param $id
     * @return View
     */
    public function show($id)
    {
        $request = request();
        if ($request->ajax()) {
            $this->getModel()->with([])->findOrFail($id);
        } else {
            return $this->view();
        }
    }

}