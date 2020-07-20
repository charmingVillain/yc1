<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    protected $fillable = ['name', 'guard_name', 'desc'];


    protected $appends = ['guard_name_text'];


    /**
     * 所有的角色类型
     * @return \Illuminate\Support\Collection
     */
    public function getGuardNameCollection()
    {
        return collect([
            'admin' => collect(['value' => 'admin', 'text' => '后台管理员角色', 'disabled' => false]),
        ]);
    }

    /**
     * 角色类型文本
     * @return string|null
     */
    public function getGuardNameTextAttribute()
    {
        $attribute = $this->getAttribute('guard_name');

        if ($this->getGuardNameCollection()->offsetExists($attribute)) {
            return $this->getGuardNameCollection()->get($attribute)->get('text');
        }

        return null;
    }

    /**
     * 同步后端菜单权限
     * @param $permissions
     */
    public function syncAdminMenu($permissions)
    {
        // 获取当前数据库中的后台菜单权限
        $old_permissions = $this->permissions()->where('guard_name', '=', Permission::ADMIN_MENU_GUARD)->get();

        $diff = $old_permissions->diff($permissions);

        if ($diff->count()) { // 删除旧的
            $this->permissions()->detach($diff);
        }

        $new = $permissions->diff($old_permissions);
        if ($new->count()) {
            $this->givePermissionTo($new);
        }
    }
}
