<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->comment('用户名');
//            $table->string('username')->unique()->comment('用户名');
            $table->string('phone')->comment('手机号码');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('email')->comment('邮箱');
            $table->string('password')->comment('密码');
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->softDeletes();
            $table->unique(['username','deleted_at']);
            $table->unique(['phone','deleted_at']);
            $table->unique(['email','deleted_at']);
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `users` comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
