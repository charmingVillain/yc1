<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    dd(\Illuminate\Support\Facades\Hash::make('password'));
    return view('welcome');
});


Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {


    Route::get('/index/create', 'IndexController@create')->name('index.create');
    Route::get('/temp', 'TempController@index')->name('temp.index');

    Route::get('login', 'LoginController@index')->name('login');
    Route::post('login', 'LoginController@login')->name('login.login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // 获取图片信息
    Route::get('file/file-info-by-id', 'FileController@getFileInfoById')->name('file.file-info-by-id');

    // 需要登录的后台
    Route::middleware(['auth'])->group(function () {

        Route::resource('test', 'TestController')->parameter('test', 'id');

        // 首页, 无需权限, 只要是后台用户
        Route::get('/', 'IndexController@index')->name('index');

        // 角色菜单
        Route::get('role/{id}/menu', 'RoleController@menu')->name('role.menu');

        // 修改角色菜单权限
        Route::put('role/{id}/update-menu', 'RoleController@updateMenu')->name('role.updateMenu');

        // 角色管理
        Route::resource('role', 'RoleController')->parameter('role', 'id');

        // 排序
        Route::put('/menu/sort/{id}', 'MenuController@sort')->name('menu.sort');
        // 菜单资源
        Route::resource('menu', 'MenuController')->parameter('menu', 'id');

        // 用户管理
        Route::get('user/{id}/roles', 'UserController@roles')->name('user.roles');
        Route::resource('user', 'UserController')->parameter('user', 'id');

        // 商品
        Route::resource('goods', 'GoodsController')->parameter('goods', 'id');

        // 商品分类
        Route::get('goods-category/parent-zero', 'GoodsCategoryController@parentZero')->name('goods-category.parent-zero');
        Route::resource('goods-category', 'GoodsCategoryController')->parameter('goods-category', 'id');


       // 图片上传
        Route::post('/file/image', 'FileController@image')->name('file.image');

        // 活动模版
        Route::resource('template', 'TemplatesController')->parameter('template', 'id');
    });


});
