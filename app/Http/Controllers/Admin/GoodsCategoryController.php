<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\NotificationException;
use App\GoodsCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodsCategoryController extends Controller
{

    /**
     * @return GoodsCategory
     */
    protected function getModel()
    {
        return (new GoodsCategory);
    }

    /**
     * 展示 分类信息
     * @param Request $request
     * @param GoodsCategory $goodsCategory
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\View\View
     */
    public function index(Request $request, GoodsCategory $goodsCategory)
    {
        if ($request->ajax()) {
            $goodsCategory->orderBy('id', 'desc');
            // 搜索  $query
            $list = $goodsCategory->with('file')->get();
            return $list;
        }
        return view('admin.goods-category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 创建 分类名称
     * @param Request $request
     * @return mixed
     * @throws NotificationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validatorModel($request);
        if ($this->getModel()->where('name', '=', $request->input('name'))->first()) {
            throw new NotificationException('分类名称重名');
        }
        $create_data['name'] = $request->input('name');
        $create_data['sort'] = empty($request->input('sort')) ? 0 : $request->input('sort');
        $create_data['pid'] = ($request->input('pid') == null) ? 0 : $request->input('pid');

        $info = $this->getModel()->create($create_data);
        if ($request->input('pid')) {
            $parent = $this->getModel()->findOrFail($request->input('pid'), ['id', 'pids']);
        }

        $info->pids = (isset($parent->pids) ? $parent->pids : 0) . ',' . $info->id;
        $info->save();

        return $info;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws NotificationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $model = $this->getModel();
        $find = $model::findOrFail($id);

        $this->validatorModel($request);

        if ($request->input('pid')) {
            $parent = $model->findOrFail($request->input('pid'), ['id', 'pids']);
        }

        if ($request->input('file_id')) {
            $find->file_id = $request->input('file_id');
        }

        foreach ($request->only(['name', 'pid', 'sort']) as $key => $value) {
            $find->{$key} = $value;
        }
        $find->pids = (isset($parent->pids) ? $parent->pids : 0) . ',' . $find->id;
        $find->save();

        return $find;
    }

    /**
     * @param $id
     * @return string
     * @throws NotificationException
     */
    public function destroy($id)
    {
        $categories = $this->getModel()->where(['pid' => $id])->orWhere('id', $id);
        $list = (clone $categories)->get();
        if ((clone $categories)->whereHas('goods')->get()->isEmpty()) {
            // 删除平台对应  商品分类关联
            if ($categories->forceDelete()) {
                return '删除成功';
            }
        }
        throw  new NotificationException('删除失败，可能此类目或其子类目下还有商品没删除！');
    }

    /**
     * 验证数据
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorModel(Request $request): void
    {
        $table = $this->getModel()->getTable();
        $rules = [
            'name' => ['required', 'min: 2', 'max:100'],
            'pid' => ['nullable'],
        ];
        if ($request->input('pid')) {
            array_push($rules, ['pid' => ["exists:$table"]]);
        }
        $this->validate($request, $rules);
    }

    /**
     * 获取 父级 数据
     * @return mixed
     */
    public function parentZero()
    {
        return $this->getModel()
            ->where('pid', '=', 0)
            ->where('status', '=', 1)
            ->get(['id', 'pid', 'name']);
    }
}
