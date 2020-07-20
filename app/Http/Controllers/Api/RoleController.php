<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected function getModel(){
        return new Role();
    }

    public function guards()
    {
        return $this->getModel()->getGuardNameCollection()->values();
    }

}
