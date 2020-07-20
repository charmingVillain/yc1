<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * 后台菜单权限
     */
    const ADMIN_MENU_GUARD = 'admin';

    protected $fillable = ['name', 'guard_name', 'desc'];
}
