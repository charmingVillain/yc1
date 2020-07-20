<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'pid', 'file_id','pids', 'status', 'sort'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodscategory()
    {
        return $this->hasMany(self::class, 'pid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany(Goods::class, 'goods_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file(){
        return $this->belongsTo(File::class);
    }
}

