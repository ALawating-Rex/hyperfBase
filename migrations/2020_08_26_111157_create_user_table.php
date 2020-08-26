<?php

use App\Util\Account;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Hyperf\DbConnection\Db;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 90)->comment('姓名');
            $table->string('username', 90)->comment('帐号')->unique();
            $table->string('phone', 20)->comment('手机号')->unique();
            $table->string('password')->default('')->comment('密码');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态 1-正常 2-禁用');
            $table->integer('role')->default(1)->comment('角色');
            $table->timestamps();
        });
        $password = 'aex.hyperfBase';
        $password_hash = Account::makePassword($password);
        DB::table('user')->insert([
            ['id' => 1, 'name' => 'admin', 'username' => 'admin', 'phone' => '12345678900', 'password' => $password_hash, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
