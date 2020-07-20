<?php

use Illuminate\Database\Seeder;

class TemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('templates')->delete();
        
        \DB::table('templates')->insert(array (
            0 => 
            array (
                'id' => 6,
                'title' => '模版测试',
                'content' => '8080酒吧最新活动',
                'remark' => '测试一下',
                'url' => 'http://jmft_cxh.test/admin/template/create',
                'file_id' => 2,
                'templates_id' => 'sad2111aasdf2a1dasds21',
                'created_at' => '2020-07-19 19:49:58',
                'updated_at' => '2020-07-19 19:49:58',
            ),
        ));
        
        
    }
}