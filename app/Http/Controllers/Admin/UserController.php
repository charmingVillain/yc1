<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\NotificationException;
use App\Rules\PhoneRule;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function getModel()
    {
        return new User();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();

        if ($request->ajax()) {
            $model = $this->getModel()->with(['roles'])->orderBy('id', 'desc');

            if ($name = $request->input('name')) {
                $model->where('name', 'like', "%$name%");
            }

            if ($email = $request->input('email')) {
                $model->where('email', 'like', "%$email%");
            }

            // 支持搜索
            if ($request->header('page-simple') == 'true') {
                return $model->simplePaginate($request->input('pageSize'));
            }

            return $model->paginate($request->input('pageSize'));
        }

        return $this->view();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->getModel()->register($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->getModel()->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->view();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'password' => ['nullable', 'min:8', 'max: 20'],
            'nickname' => ['required', 'min:2', 'max: 100'],
        ]);

        $find = $this->getModel()->findOrFail($id);

        if ($request->input('password')) { // 需要修改密码
            // 验证修改人的密码
            if (!Hash::check($request->input('admin_password'), $request->user()->getAuthPassword())) {
                throw new NotificationException('密码不正确');
            }
            $find->password = Hash::make($request->input('password'));
        }

        $find->nickname = $request->input('nickname');

        $find->save();

        return $find;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view()
    {
        return view('admin.user.index');
    }


    /**
     * 同步用户角色
     * @param Request $request
     * @return User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setRoles(Request $request)
    {

        $this->validate($request, [
            'roles' => ['required', 'array'],
            'user_id' => ['required', 'integer']
        ]);

        $user = $this->getModel()->findOrFail($request->input('user_id'));

        $roles = get_role_model()->find($request->input('roles'));

        $user->syncRoles($roles, 'admin');

        return $user;
    }

    /**
     * 用户的角色
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function roles(Request $request, $id)
    {
        $user = $this->getModel()->with([])->findOrFail($id);

        $roles = $user->roles;
        return [
            'all' => get_role_model()->get(),
            'roles' => $roles
        ];
    }

}
