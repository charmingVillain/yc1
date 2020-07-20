<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menus')->delete();
        
        \DB::table('menus')->insert(array (
            0 => 
            array (
                'id' => 1,
                'uri' => 'GET|HEAD:admin/temp',
                'name' => '系统管理',
                'pid' => 0,
                'pids' => '0,1',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-03-01 04:48:28',
                'updated_at' => '2020-03-01 04:48:28',
            ),
            1 => 
            array (
                'id' => 2,
                'uri' => 'GET|HEAD:admin/role',
                'name' => '角色管理',
                'pid' => 1,
                'pids' => '0,1,2',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 1,
                'is_ajax' => 0,
                'created_at' => '2020-03-01 04:49:45',
                'updated_at' => '2020-03-01 04:50:08',
            ),
            2 => 
            array (
                'id' => 3,
                'uri' => 'GET|HEAD:admin/menu',
                'name' => '菜单管理',
                'pid' => 1,
                'pids' => '0,1,3',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-03-01 04:50:00',
                'updated_at' => '2020-03-01 04:50:00',
            ),
            3 => 
            array (
                'id' => 4,
                'uri' => 'GET|HEAD:admin/user',
                'name' => '用户管理',
                'pid' => 1,
                'pids' => '0,1,4',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-03-06 20:38:04',
                'updated_at' => '2020-03-06 20:38:04',
            ),
            4 => 
            array (
                'id' => 5,
                'uri' => 'GET|HEAD:admin/role/{id}/menu',
                'name' => '角色菜单权限',
                'pid' => 2,
                'pids' => '0,1,2,5',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 1,
                'created_at' => '2020-03-06 20:38:33',
                'updated_at' => '2020-03-06 20:39:23',
            ),
            5 => 
            array (
                'id' => 6,
                'uri' => 'GET|HEAD:admin/user/create',
                'name' => '新增',
                'pid' => 4,
                'pids' => '0,1,4,6',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 1,
                'created_at' => '2020-03-06 20:39:07',
                'updated_at' => '2020-03-06 20:39:07',
            ),
            6 => 
            array (
                'id' => 7,
                'uri' => 'GET|HEAD:admin/user/{id}/edit',
                'name' => '编辑',
                'pid' => 4,
                'pids' => '0,1,4,7',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 1,
                'created_at' => '2020-03-06 20:40:13',
                'updated_at' => '2020-03-06 20:40:13',
            ),
            7 => 
            array (
                'id' => 8,
                'uri' => 'GET|HEAD:admin/goods',
                'name' => '酒吧列表',
                'pid' => 0,
                'pids' => '0,8',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-07-11 12:23:07',
                'updated_at' => '2020-07-11 12:23:07',
            ),
            8 => 
            array (
                'id' => 9,
                'uri' => 'GET|HEAD:admin/goods-category',
                'name' => '酒吧分类',
                'pid' => 0,
                'pids' => '0,9',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-07-11 12:23:34',
                'updated_at' => '2020-07-11 12:23:34',
            ),
            9 => 
            array (
                'id' => 10,
                'uri' => 'GET|HEAD:admin/template',
                'name' => '模版管理',
                'pid' => 0,
                'pids' => '0,10',
                'icon' => 'fa fa-cog',
                'guard_name' => 'admin',
                'status' => 1,
                'sort' => 0,
                'is_ajax' => 0,
                'created_at' => '2020-07-19 19:33:14',
                'updated_at' => '2020-07-19 19:33:14',
            ),
        ));
        
        
    }
}