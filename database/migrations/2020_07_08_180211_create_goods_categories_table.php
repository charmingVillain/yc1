<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('名称');
            $table->bigInteger('pid')->index()->comment('上级id');
            $table->string('pids')->default('')->comment('上级树');
            $table->tinyInteger('status')->default(1)->comment('状态：1:展示, 0:隐藏');
            $table->bigInteger('file_id')->nullable()->index()->comment('文件编号');
            $table->integer('sort')->default(1)->index()->comment('排序规则: 越小越靠前');
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
        Schema::dropIfExists('goods_categories');
    }
}
