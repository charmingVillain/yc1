<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {


    // 阿里sts授权
    Route::get('ali/sts', 'AliController@sts')->name('ali.sts');
    // 角色保护对象
    Route::get('role/guards', 'RoleController@guards')->name('role.guards');

    // 登录
    Route::post('auth/login', 'AuthController@login')->name('auth.login');
    Route::post('auth/refresh-token', 'AuthController@refreshToken')->name('auth.refreshToken');
    Route::middleware('auth:api')->group(function () {
        // 用户信息
        Route::get('auth/user', 'AuthController@user')->name('auth.user');
    });

    // 获取商品分类
    Route::get('goods/category', 'GoodsController@category')->name('goods.category');

    Route::get('goods/index', 'GoodsController@index')->name('goods.index');
    // 获取附近酒吧信息
    Route::get('goods/neighbouring', 'GoodsController@neighbouring')->name('goods.neighbouring');

    // 获取推荐酒吧信息
    Route::get('goods/recommend', 'GoodsController@recommend')->name('goods.recommend');

    // 轮播图
    Route::get('goods/rotation', 'GoodsController@rotation')->name('goods.rotation');

    // 获取筛选酒吧信息
    Route::get('goods/goods', 'GoodsController@goods')->name('goods.goods');

    // 获取酒吧详情信息
    Route::get('goods/detail', 'GoodsController@detail')->name('goods.detail');
    
});
