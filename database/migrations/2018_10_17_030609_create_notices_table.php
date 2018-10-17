<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->increments('id')->comment('id');
            $table->string('title')->nullable()->comment('标题');
            $table->string('editor')->nullable()->comment('发布人');
            $table->string('role')->nullable()->comment('角色');
            $table->string('dept')->nullable()->comment('部门');
            $table->string('user')->nullable()->comment('用户');
            $table->longText('content')->nullable()->comment('内容');
            $table->timestamp('datetime')->nullable()->comment('时间');
        });
        DB::statement("ALTER TABLE `notices` comment '消息' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notices');
    }
}
