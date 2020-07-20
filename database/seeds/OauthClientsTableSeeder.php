<?php

use Illuminate\Database\Seeder;

class OauthClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_clients')->delete();
        
        \DB::table('oauth_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => NULL,
                'name' => 'Health Personal Access Client',
                'secret' => '4laGIgdDeptElHFZz1gBpWQyRnOmlJXXSzGlYXqz',
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
                'created_at' => '2020-03-02 02:45:56',
                'updated_at' => '2020-03-02 02:45:56',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => NULL,
                'name' => 'Health Password Grant Client',
                'secret' => 'TsuOc70ItJduwC7jIdxxvwC3h8ybtV41seaQU7Bv',
                'redirect' => 'http://localhost',
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
                'created_at' => '2020-03-02 02:45:56',
                'updated_at' => '2020-03-02 02:45:56',
            ),
        ));
        
        
    }
}