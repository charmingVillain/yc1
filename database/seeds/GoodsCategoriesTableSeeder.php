<?php

use Illuminate\Database\Seeder;

class GoodsCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('goods_categories')->delete();
        
        \DB::table('goods_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '清吧',
                'pid' => 0,
                'pids' => '0,1',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:58:00',
                'updated_at' => '2020-07-11 12:58:00',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '迪吧',
                'pid' => 0,
                'pids' => '0,2',
                'status' => 1,
                'sort' => 2,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:58:20',
                'updated_at' => '2020-07-11 12:58:20',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '小酒馆',
                'pid' => 0,
                'pids' => '0,3',
                'status' => 1,
                'sort' => 3,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:58:44',
                'updated_at' => '2020-07-11 12:58:44',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'KTV',
                'pid' => 0,
                'pids' => '0,4',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:58:52',
                'updated_at' => '2020-07-11 12:58:52',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '商务吧',
                'pid' => 0,
                'pids' => '0,5',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:59:06',
                'updated_at' => '2020-07-11 12:59:06',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '清吧1',
                'pid' => 1,
                'pids' => '0,1,6',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 12:59:44',
                'updated_at' => '2020-07-11 12:59:44',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '迪吧1',
                'pid' => 2,
                'pids' => '0,2,7',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 13:00:01',
                'updated_at' => '2020-07-11 13:00:01',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => '小酒馆1',
                'pid' => 3,
                'pids' => '0,3,8',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 13:00:15',
                'updated_at' => '2020-07-11 13:00:15',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'KTV1',
                'pid' => 4,
                'pids' => '0,4,9',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 13:00:25',
                'updated_at' => '2020-07-11 13:00:25',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => '商务吧1',
                'pid' => 5,
                'pids' => '0,5,10',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 13:00:35',
                'updated_at' => '2020-07-11 13:00:35',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => '测试酒吧',
                'pid' => 0,
                'pids' => '0,11',
                'status' => 1,
                'sort' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 17:40:50',
                'updated_at' => '2020-07-11 17:40:50',
            ),
        ));
        
        
    }
}