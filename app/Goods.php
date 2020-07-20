<?php

namespace App;

use App\Exceptions\NotificationException;
use App\Traits\CreateUniqueNo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Goods extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'goods_code', 'name', 'title', 'goods_category_id', 'sales_price', 'market_price',
        'detail', 'img_id', 'sort', 'sales_number', 'status','abscissa','ordinate','tags','lng','lat','address'
    ];
    protected $appends = [
        'status_text',
    ];

    /**
     * 上架状态
     * @return \Illuminate\Support\Collection
     */
    public function getStatus()
    {
        return collect([
            1 => collect(['text' => '上架', 'value' => 1]),
            2 => collect(['text' => '下架', 'value' => 2]),
        ]);
    }

    public function getStatusTextAttribute()
    {
        $attribute = $this->getAttribute('status');
        return $this->getStatus()->offsetExists($attribute) ? $this->getStatus()->get($attribute)->get('text') : null;
    }

    public function getTagsAttribute($tags)
    {
        return $tags ? explode(',', $tags) : null;
    }

    /**
     * 商品主图
     * @return |null
     */
    public function getGoodsImgAttribute()
    {
        return $this->img ? $this->img->url : null;
    }

    /**
     * 商品主图
     * @return |null
     */
    public function img()
    {
        return $this->belongsTo(\App\File::class, 'img_id');
    }

    /**
     * 文件模型关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'goods_images', 'goods_id', 'file_id')->withTimestamps();
    }

    /**
     * 商品分类
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goodsCategory()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    /**
     * 订单的前缀代表的意思
     * @return \Illuminate\Support\Collection
     */
    public static function getCreateUniqueNoPrefixes()
    {
        return collect([
            'S' => collect(['value' => 'S', 'text' => '直营商品', 'belongs_type' => 1]),
            'P' => collect(['value' => 'P', 'text' => '供应商商品', 'belongs_type' => 2]),
        ]);
    }


    /**
     * 生成商品编号
     * @param string $prefix
     * @return string
     */
    public function getNo($prefix = 'YC')
    {
        $time_str = Carbon::now()->format('YmdHis');
        $max = 99999;
        $first = $this->where('goods_code', 'like', "$prefix$time_str")->orderBy('id', 'desc')->first(['id', 'goods_code']);
        $no = '';
        if (empty($first)) {
            $no = $prefix . $time_str . sprintf('%05s', 1);
        } else {
            $next = intval(Str::after($first->goods_code, $prefix . $time_str)) + 1;
            if ($next > $max) {
                sleep(1);
                return $this->getNo($prefix);
            } else {
                $no = $prefix . $time_str . sprintf('%05s', $next);
            }
        }
        if (empty($this->where('goods_code', '=', $no)->first('id'))) {
            return $no;
        } else {
            return $this->getNo($prefix);
        }
    }
}
