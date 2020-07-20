<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{

    protected function redirectTo()
    {
        return route('admin.index');
    }

    public function username()
    {
        return 'name';
    }

    public function index()
    {
        return view('admin.login.index');
    }

    /**
     * 登出跳转
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect(route('admin.login'));
    }


}
