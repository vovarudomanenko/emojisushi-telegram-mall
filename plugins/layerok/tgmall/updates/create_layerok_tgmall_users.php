<?php

namespace Layerok\TgMall\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateLayerokTgMallUsers extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_users', function ($table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')->nullable();
            $table->string('chat_id')->nullable();
            $table->json('state')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_users');
    }
}
