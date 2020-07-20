<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('end_at')->nullable()->comment('过期时间');
            $table->string('key')->comment('键名');
            $table->string('group')->index()->comment('组');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_keys');
    }
}
