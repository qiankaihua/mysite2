<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nickname');
            $table->boolean('gender')->default(0)->comment('0 for man and 1 for women');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('realname')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('user')->insert([
            'username' => 'administrator',
            'password' => app('hash')->make(sha1('administrator')),
            'nickname' => 'administrator',
            'gender' => 0,
            'email' => 'admin@qiankaihua.top',
            'avatar' => null,
            'phone' => null,
            'birthday' => '1997-11-07 20:00:00',
            'realname' => 'qiankaihua',
            'created_at' => '1997-11-07 20:00:00',
            'updated_at' => '1997-11-07 20:00:00',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
