<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'admin',
                'desc' => '后台管理员',
                'created_at' => '2020-03-01 02:28:05',
                'updated_at' => '2020-03-06 20:44:11',
            ),
        ));
        
        
    }
}