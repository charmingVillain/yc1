<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplatesController extends Controller
{

    /**
     * @return Template
     */
    public function getModel()
    {
        return (new Template());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view()
    {
        return view('admin.template.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->getModel()
                ->with('file')
                ->orderBy('id', 'desc');

            // 支持搜索
            if ($request->header('page-simple') == 'true') {
                $goods = $model->simplePaginate($request->input('pageSize'));
            }

            $template = $model->paginate($request->input('pageSize'));

            return $template;

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
        return $this->getModel()->newQuery()->with('file')->where('id', $id)->first();
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
        $model = $this->getModel()->findOrFail($id);
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);
            return $model;
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
        return DB::transaction(function () use ($data) {
            $model = $this->getModel();
            $template = $model->create($data);
            return $template;
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
