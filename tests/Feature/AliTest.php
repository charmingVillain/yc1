<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use ShaoZeMing\AliSTS\Services\STSService;
use Tests\TestCase;

class AliTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function testSts()
    {
        $config = config('ali.oss');

        $sts = new STSService($config);

        $result =  $sts->getToken(); // 获取播放权限参数

        dd($result);
    }
}
