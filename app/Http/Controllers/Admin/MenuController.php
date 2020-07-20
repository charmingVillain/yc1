<?php

namespace App\Http\Controllers\Admin;

use App\CacheManger;
use App\Menu;
use App\SimpleResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exceptions\NotificationException;

class MenuController extends Controller
{


    public function getModel()
    {
        return new Menu;
    }
    /**
     * 收银后台菜单
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->getModel()->where('guard_name','admin')->orderBy('sort', 'desc')->orderBy('id', 'desc');
            // 支持搜索
            return $model->get();
        }
        return view('admin.menu.index')->with('route_list', $this->getRouteList()->values());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getRouteList()
    {
        return get_route_information_list()->filter(function ($route) {
            return Str::startsWith($route['uri'], 'admin');
        });
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
     * @param Request $request
     * @return mixed
     * @return Menu
     * @throws \Throwable
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $store =  $this->getModel()->storeByArr(array_filter($request->only(['name', 'uri', 'pid', 'icon', 'is_ajax','guard_name'])));

        CacheManger::clearAdminMenu();

        return $store;
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


    public function update(Request $request, $id)
    {
        //
        $this->validateRequest($request);

        $find = $this->getModel()->findOrFail($id);

        $find =  DB::transaction(function () use ($request, $find) {

            foreach (array_filter($request->only(['name', 'uri', 'pid', 'icon', 'is_ajax']), function ($val) {
                return !is_null($val);
            }) as $key => $value) {
                $find->{$key} = $value;
            }

            $find->save();

            if ($request->input('pid')) {
                $parent = $this->getModel()->find($request->input('pid'), ['id', 'pids']);
            }

            $find->pids = (isset($parent->pids) ? $parent->pids : 0) . ',' . $find->id;

            $find->save();

            return $find;
        });

        CacheManger::clearAdminMenu();

        return $find;
    }

    /**
     * @param $id
     * @return SimpleResponse
     * @throws NotificationException
     */
    public function destroy($id)
    {
        $find = $this->getModel()->findOrFail($id);

        if ($find->children()->count()) {
            throw new NotificationException('请先移除下级菜单');
        }

        $find->delete();

        CacheManger::clearAdminMenu();

        return SimpleResponse::success('删除成功');
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateRequest(Request $request): void
    {
        $this->validate($request, [
            'uri' => ['required', 'max:200'],
            'name' => ['required', 'max:200'],
            'pid' => ['required', 'int'],
            'icon' => ['max:200'],
            'is_ajax' => ['nullable', 'boolean']
        ]);
    }


    public function sort(Request $request, $id)
    {
        $find = $this->getModel()->findOrFail($id);

        $this->validate($request, [
            'end_id' => ['required'],
            'type' => ['required', 'in:before,after,inner']
        ]);

        $end = $this->getModel()->findOrFail($request->input('end_id'));

        // 排序
        $find->sortMenu($end, $request->input('type'));

        CacheManger::clearAdminMenu();

        return SimpleResponse::success('排序成功');
    }
}
