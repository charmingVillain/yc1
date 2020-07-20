<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/3/18
 * Time: 9:21
 */

use App\OrderProfitSharing;
use App\PointDivide;
use Illuminate\Support\Facades\Cache;
use App\User;

/**
 * 获取权限model
 */
if (!function_exists('get_permission_model')) {
    function get_permission_model()
    {
        return app()->make(config('permission.models.permission'));
    }
}


if (!function_exists('get_role_model')) {
    /**
     * 获取角色model
     * @return \App\Role
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function get_role_model()
    {
        return app()->make(config('permission.models.role'));
    }
}

/**
 * 获取路由信息
 */
if (!function_exists('get_route_information')) {
    function get_route_information(\Illuminate\Routing\Route $route)
    {
        return [
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => ltrim($route->getActionName(), '\\'),
        ];
    }
}


if (!function_exists('get_route_information_list')) {
    /**
     * 获取所有路由的基本信息
     * @return \Illuminate\Support\Collection
     * @throws
     */
    function get_route_information_list()
    {
        $router = app()->make(\Illuminate\Routing\Router::class);

        return collect($router->getRoutes())->map(function ($route) {
            return get_route_information($route);
        })->filter(function ($routeInfo) {
            return !empty($routeInfo['uri']);
        })->map(function ($routeInfo) {
            $routeInfo['value'] = $routeInfo['method'] . ':' . $routeInfo['uri'];
            return $routeInfo;
        });
    }
}


if (!function_exists('list_to_tree')) {
    /** 将数组转换成 tree结构的数据
     * @param array $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        $tree = array();
        if (is_array($list)) {
            //创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                //判断是否存在parent
                $parantId = $data[$pid];
                if ($root == $parantId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parantId])) {
                        $parent = &$refer[$parantId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}


if (!function_exists('array_pk')) {
    /**
     * 数组组件化
     * @param $list
     * @param string $pk
     * @return array
     */
    function array_pk($list, $pk = 'id')
    {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = $list[$key];
        }
        return $refer;
    }
}


if (!function_exists('is_array_data_get')) {
    /**
     * 判断是不是一个数组
     * @param mixed $data
     * @param string $key
     * @return bool
     */
    function is_array_data_get($data, $key)
    {
        return is_array(data_get($data, $key));
    }
}

if (!function_exists('has_menu_children')) {
    /**
     * 是否有下级菜单
     * @param $data
     * @param $key
     * @return bool
     */
    function has_menu_children($data, $key)
    {
        $arr = data_get($data, $key);
        if (is_array($arr)) {
            return count(array_filter($arr, function ($val) {
                return !data_get($val, 'is_ajax');
            })) > 0;
        }
        return false;
    }
}


if (!function_exists('replace_if_exist_in_array')) {

    /**
     * 替换数组中的值为指定的值
     * @param array $data 要修改的数组
     * @param array $need_replace 需要替换的值的key
     * @param string $replace 替换成的值
     * @return array
     */
    function replace_if_exist_in_array(array $data, array $need_replace, string $replace = '***'): array
    {
        array_walk_recursive($data, function (&$val, $key) use ($need_replace, $replace) {
            if (in_array($key, $need_replace, true)) {
                $val = $replace;
            }
            return $val;
        });
        return $data;
    }
}

if (!function_exists('uc_sign')) {
    /**
     * 获取uc 的签名
     * @param array $data
     * @return array
     */
    function uc_sign(array $data)
    {
        $str = "";
        foreach ($data as $key => $value) {
            $str .= $key;
            $str .= $value;
        }
        $data['keys'] = 'money';
        $data['timestamp'] = time();
        $str .= trim($data['timestamp']);
        $str .= 'Re7EsjIBJixLVXFFTDvPwQyHuyHMlCng';
        $str = md5($str);
        $data['sign'] = $str;
        return $data;
    }
}

if (!function_exists('onlyArray')) {
    /**
     * 自定义数组only
     * @param array $array
     * @param array $keys
     * @return array
     */
    function onlyArray(array $array, array $keys)
    {
        $data = [];
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $data[last(explode('.', $key))] = data_get($array, $key);
            }
        }
        return $data;
    }
}

/**
 * 获取用户菜单访问权限的key
 * @param User $user
 * @return string
 */
function get_cache_permission_menus_key(User $user)
{
    return __METHOD__ . $user->id;
}

/**
 * 清空获取用户菜单访问权限
 * @param User $user
 * @return \Illuminate\Support\Collection
 */
function get_cache_permission_menus(User $user)
{
    $key = get_cache_permission_menus_key($user);

    return session()->get($key, function () use ($key, $user) {
        $permissions = $user->getAllPermissions()->filter(function (\App\Permission $permission) {
            return $permission->guard_name == 'admin';
        })->filter();
        session()->put($key, $permissions);
        return $permissions;
    });
}

/**
 * 设置缓存
 * @param User $user
 * @param $permissions
 */
function set_cache_permission_menus(User $user, $permissions)
{
    $key = get_cache_permission_menus_key($user);
    session()->put($key, $permissions);
}

/**
 * 清空用户菜单访问权限
 * @param User $user
 */
function clear_cache_permission_menus(User $user)
{
    $key = get_cache_permission_menus_key($user);
    session()->forget($key);
}


if (!function_exists('http_build_url')) {

    /**
     * 构建url
     * @param $url
     * @param array|null $args
     * @return string
     */
    function http_build_url($url, ?array $args = null)
    {
        $parse_url = parse_url($url);
        $scheme = data_get($parse_url, 'scheme');
        $host = data_get($parse_url, 'host');
        $query = data_get($parse_url, 'query');
        $path = data_get($parse_url, 'path', '');
        $port = data_get($parse_url, 'port', '');
        if ($port) {
            $port = ':' . $port;
        }
        $parse_query = \GuzzleHttp\Psr7\parse_query($query);
        $build_query = http_build_query(array_merge($parse_query, $args ?? []));
        return $scheme . '://' . $host . $port . $path . '?' . $build_query;
    }
}

/**
 * xml 转化为数组
 * @param $value
 * @return array
 */
if (!function_exists('xml_to_arr')) {
    function xml_to_arr($value)
    {
        $temp = is_string($value) ?
            simplexml_load_string($value, 'SimpleXMLElement', LIBXML_NOCDATA) :
            $value;

        $result = [];

        foreach ((array)$temp as $key => $value) {
            if ($key === "@attributes") {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $result['_' . $k] = $v;
                    }
                } else {
                    $result['_' . key($value)] = $value[key($value)];
                }

            } elseif (is_array($value) && count($value) < 1) {
                $result[$key] = '';
            } else {
                $result[$key] = (is_array($value) or is_object($value)) ? xml_to_arr($value) : $value;
            }
        }

        return $result;
    }
}

/**
 * 返回非null 的数据
 * @param array $arr
 * @return array
 */
function filter_arr_null(array $arr)
{
    return array_filter($arr, function ($value) {
        return !is_null($value);
    });
}

/**
 * 吧null 过滤为 '' 字符
 * @param array $arr
 * @return array
 */
function filter_arr_null_to_empty(array $arr)
{
    return array_map(function ($value) {
        return is_null($value) ? '' : $value;
    }, $arr);
}

/**
 * @param $str
 * @param int $l
 * @return array|array[]|false|string[]
 */
function str_split_unicode($str, $l = 0)
{
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}


/**
 * 解析cookie 的字符串
 * @param $str
 * @return \Illuminate\Support\Collection
 */
function parse_cookie_str($str)
{
    $temp = collect();
    foreach (explode(';', $str) as $s) {
        $data = explode('=', $s);
        $key = data_get($data, 0);
        $value = data_get($data, 1);
        if (!is_null($key) && !is_null($value)) {
            $key = trim($key);
            $value = trim($value);
            $temp->offsetSet($key, $value);
        }
    }
    return $temp;
}

/**
 * 字符串截取
 * @param $value
 * @param int $limit
 * @param string $end
 * @return string
 */
function str_limit_except_chinese($value, $limit = 100, $end = '...')
{
    if (strlen($value) <= $limit) {
        return $value;
    }
    return rtrim(mb_substr($value, 0, $limit, 'utf-8')) . $end;
}

/**
 * 当前月份是一个季度的最后一个月
 * @return bool
 */
function isLastMonthOfQuarter()
{
    return in_array(now()->format('m'), ['03', '06', '09', '12']);
}

/**
 * 获取下一度的最后一个月
 * @return \Illuminate\Support\Carbon
 */
function getLastQuarter()
{
    return now()->addQuarter()->endOfQuarter();
}

/**
 *  获取本季度的最后一个月
 * @return \Carbon\Carbon
 */
function getThisQuarter()
{
    return now()->endOfQuarter();
}

/**
 * 是否在月底的 $num 天内
 * @param int $num
 * @return bool
 */
function isInLastOfMonth(int $num)
{
    return now()->endOfMonth()->day - now()->day <= $num;
}


/**
 * 获取后台菜单
 * @return array
 */
function get_admin_menu()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    $menus = $user->getAdminMenu();
    if (count($menus)) {
        // 获取当前得菜单
        $currentRoute = get_route_information(\Illuminate\Support\Facades\Route::current());
        $uri = $currentRoute['method'].":".$currentRoute['uri'];
        // 匹配当前活动得菜单
        $matches = collect($menus)->filter(function ($item) use ($uri) {
            return $uri == $item['uri'];
        });
        // todo 如何有多个那么获取一下前端得辅助数据id
        // 设置当前得菜单得上级都是活动菜单
        $menusPk = array_pk($menus);
        $matches->each(function ($item) use (&$menusPk) {
            set_active_by_id($menusPk, $item['id']);
        });
        $menus = array_values($menusPk);
    }
    $tree = list_to_tree($menus);
    return $tree;
}


/**
 * @param $menusPk
 * @param $id
 */
function set_active_by_id(&$menusPk, $id)
{
    $item = $menusPk[$id];

    if ($menusPk[$id]) {
        $item['active'] = true;
        data_set($menusPk[$id], 'active', true);
        $pid = data_get($item, 'pid');
        if ($pid) {
            set_active_by_id($menusPk, $pid);
        }
    }

}
