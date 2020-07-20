<?php

namespace App\Http\Controllers\Admin;

use App\Goods;
use App\Http\Controllers\Controller;
use App\Traits\SimpleResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

    use SimpleResponse;

    /**
     * @return Goods
     */
    public function getModel()
    {
        return (new Goods());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view()
    {
        return view('admin.goods.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \App\Exceptions\NotificationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->getModel()
                ->with(['img','goodsCategory'])
                ->orderBy('id', 'desc');

            //搜索名称或商品编号查询
            if ($name = $request->input('name')) {
                $model->where(function ($query) use ($name) {
                    $query->orWhere('name', 'like', "%$name%")
                        ->orWhere('title', 'like', "%$name%");
                });
            }

            //商品类目查询
            $goodsCategoryIds = $request->input('goods_category_ids');
            if ($goodsCategoryIds) {
                $model->where(function ($query) use ($goodsCategoryIds) {
                    $query->whereIn('goods_category_id', $goodsCategoryIds);
                });
            }

            // 支持搜索
            if ($request->header('page-simple') == 'true') {
                $goods = $model->simplePaginate($request->input('pageSize'));
            }

            $goods = $model->paginate($request->input('pageSize'));

            $goods->map(function ($item) {
                $item->append('goods_img');
                return $item;
            });

            return $goods;

        }
        return $this->view();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->view();
    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->getModel()->newQuery()->with('files')->where('id',$id)->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->view();
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $goods = $this->getModel()->findOrFail($id);
        $data['img_id'] = collect($data['file_ids'])->first();

        $data['tags'] = implode(',', $data['tags']);
        $file_ids = $data['file_ids'];
        unset($data['file_ids']);

        return DB::transaction(function () use ($goods,$file_ids,$data) {
            $goods->update($data);
            $goods->files()->sync($file_ids);

            return $goods;
        });
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['img_id'] = collect($data['file_ids'])->first();
        $data['tags'] = implode(',', $data['tags']);

        $file_ids = $data['file_ids'];
        unset($data['file_ids']);
        return DB::transaction(function () use ($file_ids,$data) {
            $model = $this->getModel();
            $data['goods_code'] = $model->getNo();
            $goods = $model->create($data);
            $goods->files()->sync($file_ids);

            return $goods;
        });
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        $this->getModel()->findOrFail($id)->delete();
    }
}
