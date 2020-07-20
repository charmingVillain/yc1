<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
       dd(Hash::make('password'));
    }


    public function testCreate()
    {

        $user = User::with([])->firstOrCreate([
            'email' => '563010570@qq.com',
            'name' => 'dengxiawu'
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'nickname' => '夏日炎炎'
        ]);

        dd($user->toArray());
    }
}
