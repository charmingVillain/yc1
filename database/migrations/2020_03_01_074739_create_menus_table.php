<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uri')->index()->comment('菜单地址');
            $table->string('name')->comment('菜单名称');
            $table->bigInteger('pid')->index()->default(0)->comment('上级编号');
            $table->string('pids', 500)->nullable()->comment('上级编号数');
            $table->string('icon')->default('')->comment('图标');
            $table->string('guard_name')->default('admin')->comment('菜单类型');
            $table->tinyInteger('status')->default(1)->comment('状态(1:展示, 0:隐藏)');
            $table->integer('sort')->default(0)->index()->comment('排序规则: 越大越靠前');
            $table->boolean('is_ajax')->default(false)->comment('侧边栏不显示');
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
        Schema::dropIfExists('menus');
    }
}
