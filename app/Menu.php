<?php

namespace App;

use App\Events\MenuChangePidsEvent;
use App\Exceptions\NotificationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $fillable = ['uri', 'name', 'pid', 'icon', 'is_ajax', 'guard_name'];

    public function getStatus()
    {
        return collect([
            0 => collect(['value' => 0, 'text' => '隐藏']),
            1 => collect(['value' => 1, 'text' => '展示']),
        ]);
    }


    public static function getGuardName()
    {
        return collect([
            'admin' => collect(['value' => 'admin', 'text' => '系统后台菜单','type'=>'admin']),
        ]);
    }

    /**
     * 菜单树形 列表
     */
    public function tree()
    {
        $list = $this->orderBy('sort', 'desc')->orderBy('id', 'desc')->get()->toArray();
        $this->model_tree = $list;
        $tree_list = list_to_tree($list);
        return $tree_list;
    }

    /**
     * 获取当前菜单下的子集菜单
     * @param null|int $id 菜单编号
     * @return mixed
     * @throws NotificationException
     */
    public function children($id = null)
    {
        $pids = $this->pids;
        if (!is_null($id)) {
            $info = $this->findOrFail($id);
            $pids = $info->pids;
        }
        if (empty($pids)) {
            throw new NotificationException('菜单对象未取得');
        }
        return $this->where('pids', 'like', $pids . ',%')->get();
    }

    /**
     * @param Menu $end
     * @param String $type before: 在end节点前 after: 在end节点后 inner: 放到end节点里
     * @return $this
     * @throws NotificationException
     */
    public function sortMenu(Menu $end, $type)
    {
        if (empty($this->id)) {
            throw new NotificationException('菜单对象未取得');
        }
        $oldPid = $this->pid;
        switch ($type) {
            case 'before':
                $this->pid = $end->pid;
                $this->save();
                // 排列一下同级上面的数据
                $this->sortOnLevel($type, $end);
                break;
            case 'after':
                $this->pid = $end->pid;
                $this->save();
                // 排列一下同级下面的数据
                $this->sortOnLevel($type, $end);
                break;
            case 'inner':
                $this->pid = $end->id;
                $this->save();
                // 修改一下pids和他下面的pids
                break;
        }
        $this->triggerMenuChangePidsEvent($oldPid);
        return $this;
    }

    public function triggerMenuChangePidsEvent($oldPid)
    {
        if ($oldPid != $this->pid) {
            event(new MenuChangePidsEvent($this));
        }
    }

    /**
     * 排列一下同级的数据
     * @param $type
     * @param Menu $end
     */
    public function sortOnLevel($type, Menu $end)
    {
        $_this = $this;
        $collection = Menu::where('pid', '=', $_this->pid)->orderBy('sort', 'desc')->orderBy('id', 'desc')->get()->filter(function ($item) use ($_this) {
            return $_this->id != $item->id;
        });

        $newCollection = collect();

        foreach ($collection as $key => $value) {
            if ($value->id == $end->id) {
                // 这里的的before 和 after 要返排序，因为要反序
                if ($type == 'before') {
                    $newCollection->push($_this);
                    $newCollection->push($value);
                } elseif ($type == 'after') {
                    $newCollection->push($value);
                    $newCollection->push($_this);
                }
            } else {
                $newCollection->push($value);
            }
        }

        return $newCollection->reverse()->values()->each(function ($item, $key) {
            $item->sort = $key;
            $item->save();
        });
    }


    /**
     * 修改相关的pids
     */
    public function changePids()
    {
        $pids = $this->pid ? $this->findOrFail($this->pid)->pids : '0';
        $this->pids = $pids . ',' . $this->id;
        $this->save();
        // 下面所有的节点
        $this->where('pid', '=', $this->id)->get()->each(function ($info) {
            event(new MenuChangePidsEvent($info));
        });
    }

    public function storeByArr($data)
    {
        return DB::transaction(function () use ($data) {
            $info = $this->getModel()->create(array_filter($data, function ($val) {
                return !is_null($val);
            }));

            if (data_get($data, 'pid')) {
                $parent = $this->getModel()->find(data_get($data, 'pid'), ['id', 'pids']);
            }

            $info->pids = (isset($parent->pids) ? $parent->pids : 0) . ',' . $info->id;

            $info->save();

            return $info;
        });
    }
}
