<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Menu;
use App\Permission;
use App\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends SimpleAbstractController
{
    protected function getModel()
    {
        return new Role();
    }

    protected function search(Request $request): Builder
    {
        $model = $this->getModel()->with([]);

        if ($name = $request->input('name')) {
            $model->where('name', 'like', "%{$name}%");
        }

        if ($guard_name = $request->input('guard_name')) {
            $model->where('guard_name', '=', $guard_name);
        }

        return $model;
    }


    /**
     * 新增
     * @param Request $request
     * @return Builder|Model
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $guard_names = $this->getModel()->getGuardNameCollection()->keys()->join(',');

        $data = $this->validate($request, [
            'desc' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', 'alpha', 'unique:roles'],
            'guard_name' => ['required', "in:{$guard_names}"]
        ]);

        return $this->getModel()->with([])->create($data);
    }


    /**
     * 修改
     * @param Request $request
     * @param $id
     * @return Role
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $guard_names = $this->getModel()->getGuardNameCollection()->keys()->join(',');

        $data = $this->validate($request, [
            'desc' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', 'alpha', Rule::unique('roles')->ignore($id)],
            'guard_name' => ['required', "in:{$guard_names}"]
        ]);

        $model = $this->getModel();
        $find = $model->with([])->findOrFail($id);
        $find->update($data);
        return $find;
    }

    /**
     * @param Request $request
     * @param $id
     * @return array|\Illuminate\Contracts\View\View
     */
    public function menu(Request $request, $id)
    {
        if ($request->ajax()) {
            $role = $this->getModel()->with([])->findOrFail($id);

            $guard_name = Permission::ADMIN_MENU_GUARD; // 后台菜单权限

            $all = (new Menu)->with([])
                ->where('guard_name', '=', $guard_name)
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->get();


            $checked_ids = $role->permissions()
                ->where('guard_name', '=', $guard_name)
                ->get(['id', 'name'])->map(function (Permission $permission) {
                    return intval($permission->name);
                })->filter()->toArray();

            return [
                'all' => $all,
                'checked_ids' => $checked_ids,
                'role' => $role
            ];
        } else {
            return $this->view();
        }
    }


    /**
     * 修改角色菜单权限
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateMenu(Request $request, $id)
    {
        $this->validate($request, [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer']
        ]);
        $role = $this->getModel()->with([])->findOrFail($id);

        // 根据菜单id 获取所有的菜单， 并且去设置权限
        $menus = Menu::whereIn('id', $request->input('ids'))->get();

        $permissions = $menus->map(function (Menu $menu) {
            return get_permission_model()->updateOrCreate([
                'name' => $menu->id,
                'guard_name' => Permission::ADMIN_MENU_GUARD,
            ], [
                'desc' => '菜单访问权限:' . $menu->name
            ]);
        });

        $role->syncAdminMenu($permissions);

        return $role;
    }

}
