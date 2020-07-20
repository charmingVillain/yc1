<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('goods_code',64)->unique()->comment('商品编号');
            $table->string('name',200)->nullable()->comment('名称');
            $table->string('title',200)->nullable()->comment('标题');
            $table->string('address',200)->nullable()->comment('地址');
            $table->bigInteger('goods_category_id')->index()->nullable()->comment('商品类目');
            $table->unsignedDecimal('sales_price',10,2)->default(0)->comment('销售价格');
            $table->unsignedDecimal('market_price',10,2)->nullable()->comment('市场原价');
            $table->text('detail')->nullable()->comment('详情');
            $table->bigInteger('img_id')->index()->nullable()->comment('商品主图id');
            $table->bigInteger('sort')->default(0)->comment('排序');
            $table->integer('sales_number')->default(0)->comment('销售数量');
            $table->tinyInteger('status')->default(1)->comment('上架状态 1：上架,2:下架');
            $table->string('tags', 500)->nullable()->comment('商品标签');
            $table->string('lng', 100)->nullable()->comment('商品经度');
            $table->string('lat', 100)->nullable()->comment('商品纬度');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
