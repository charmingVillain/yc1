<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable()->comment('标题');
            $table->string('content')->nullable()->comment('内容');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('url')->nullable()->comment('推送消息中点击跳转的链接，不填就不会跳转');
            $table->bigInteger('file_id')->nullable()->index()->comment('图文id');
            $table->string('templates_id')->comment('模版id');
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
        Schema::dropIfExists('templates');
    }
}
