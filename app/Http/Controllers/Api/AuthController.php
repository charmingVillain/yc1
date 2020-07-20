<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * 登录
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        $data = $this->validate($request, [
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6']
        ]);

        $client = new Client();

        $url = config('app.url');
        $url = Str::finish($url, '/');
        $url = Str::finish($url, 'oauth/token');

        $response = $client->post($url, [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('auth.passport.password_client_id'),
                'client_secret' => config('auth.passport.password_client_secret'),
                'username' => $data['username'],
                'password' => $data['password'],
                'scope' => 'api',
            ],
        ]);

        return $response;
    }


    public function refreshToken(Request $request)
    {
        $client = new Client();

        $url = config('app.url');
        $url = Str::finish($url, '/');
        $url = Str::finish($url, 'oauth/token');

        $refresh_token = $request->header('Refresh-Token');

        $response = $client->post($url, [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
                'client_id' => config('auth.passport.password_client_id'),
                'client_secret' => config('auth.passport.password_client_secret'),
                'scope' => 'api',
            ],
        ]);

        return $response;
    }

    /**
     * 用户信息
     * @param Request $request
     * @return User
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
