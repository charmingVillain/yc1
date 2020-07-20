<?php

namespace App;

use App\Exceptions\NotificationException;
use App\Rules\PhoneRule;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens, ValidatesRequests;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nickname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /** 获取后台菜单
     * @param null|int $id
     * @return array
     */
    public function getAdminMenu($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }

        if (empty($id)) {
            return [];
        }

        $key = $this->getAdminMenuKey($id);

        return Cache::get($key, function () use ($key) {
            if ($this->isSuperAdmin()) {
                $menus = Menu::with([])
                    ->where('guard_name', '=', Permission::ADMIN_MENU_GUARD)
                    ->where('status', '=', 1)
                    ->orderBy('sort', 'desc')
                    ->orderBy('id', 'desc')
                    ->get(['id', 'uri', 'pid', 'name', 'icon', 'is_ajax'])
                    ->toArray();
            } else {
                $menus = [];
            }

            // 过滤掉不是get得请求
            $menus = array_filter($menus, function ($val) {
                return Str::startsWith($val['uri'], 'GET|HEAD');
            });

            $menus = array_map(function ($val) {
                $val['url'] = Str::start(str_replace('GET|HEAD:', '', $val['uri']), '/');
                return $val;
            }, $menus);

            if (count($menus)) {
                // 保存缓存了的key
                $time = now()->addDay();
                $data = [
                    'key' => $key,
                    'group' => 'admin_menu',
                    'end_at' => $time
                ];
                CacheKey::store($data);
                Cache::put($key, $menus, $time);
            }
            return $menus;
        });
    }

    /**
     * 后台菜单key
     * @param $id
     * @return string
     */
    public function getAdminMenuKey($id)
    {
        return "user_{$id}_admin_menu";
    }


    public function clearAdminMenu($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        if (empty($id)) {
            return;
        }
        Cache::forget($this->getAdminMenuKey($id));
    }


    public function getAdminPermissionKey($id)
    {
        return "user_{$id}_admin_permission";
    }

    /**
     * 同步角色
     * @param $roles
     * @param null $guard
     * @return User
     * @throws NotificationException
     */
    public function syncRoles($roles, $guard = null)
    {

        if (is_null($guard)) {
            throw new NotificationException('请输入角色类型');
        } else {
            $guard = is_array($guard) ? $guard : (array)$guard;
            $this->roles()->whereIn('guard_name', $guard)->detach();
        }

        return $this->assignRole($roles);
    }

    /**
     * 授权能访问得菜单
     * @return array
     */
    public function getAdminPermissions($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }

        if (empty($id)) {
            return [];
        }

        $permissions = $this->getAllPermissions();

        $permissions->filter(function () {

        });

    }


    /**
     * 是不是超级管理员
     * @param null|int $id
     * @return bool
     */
    public function isSuperAdmin($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        return config('auth.super_admin') == $id;
    }


    /**
     * 通过给定的username获取用户实例。
     *
     * @param string $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('name', $username)->first();
    }


    /**
     * 注册
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $data = $this->validate($request, [
            'name' => ['required', 'alpha_dash', 'min:2', 'max:100'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max: 20'],
            'nickname' => ['required', 'min:2', 'max: 100'],
        ]);

        collect([
            [
                'value' => 'name',
                'text' => '用户名'
            ],
            [
                'value' => 'email',
                'text' => '邮箱'
            ],
        ])->each(function ($item) use ($request) {
            if (!$this->isNameOrEmailOrPhoneUniq($request->input(data_get($item, 'value')))) {
                throw new NotificationException(data_get($item, 'text') . '已被使用');
            }
        });

        $data['password'] = Hash::make($data['password']);

        $create = $this->create($data);

        return $create;
    }

    /**
     * 验证除去当前用户不能重复的值判断
     * @param $value
     * @return bool
     */
    public function isNameOrEmailOrPhoneUniq($value)
    {
        $user = $this->anyNameOrEmailOrPhoneSameUser($value, $this->id);
        return empty($user);
    }

    /**
     * @param string | integer $value 待验证的值
     * @param null | integer $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function anyNameOrEmailOrPhoneSameUser($value, $id = null)
    {
        $query = $this->where(function ($query) use ($value) {
            $query->orWhere('name', '=', $value)
                ->orWhere('email', '=', $value);
        });
        if (!is_null($id)) {
            $query->where('id', '<>', $this->id);
        }
        return $query->first();
    }

}
