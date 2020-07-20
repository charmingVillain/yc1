<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SimpleResponse;
use Illuminate\Http\Request;

class TempController extends Controller
{
    public function index()
    {
        return SimpleResponse::success('这是一个用来占位得地址');
    }
}
