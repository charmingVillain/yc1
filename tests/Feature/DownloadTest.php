<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
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


    protected function getFileName($url) {
        $pathinfo = pathinfo($url);

        return $pathinfo['basename'];
    }


    public function testFonts()
    {

        $client = new Client();

        $content = Storage::disk('public')->get('css.css');

        // 匹配出所有得地址信息
        preg_match_all("/https.*\.(woff2|tff)/iU", $content, $urls);


        if (isset($urls[0]) && is_array($urls[0])) {
            foreach ($urls[0] as $key => $url) {
                $fileName = $this->getFileName($url);

                dd(public_path("fonts/{$fileName}"));
                $client->request('GET', $url, ['sink' =>  public_path("fonts/{$fileName}")]);
            }
        }

        dd($urls);
    }


    public function testFile()
    {
        $file = "https://fonts.gstatic.com/s/sourcesanspro/v13/6xK1dSBYKcSV-LCoeQqfX1RYOo3qPZ7qsDJT9g.woff2";

        dd(pathinfo($file));
    }
}
