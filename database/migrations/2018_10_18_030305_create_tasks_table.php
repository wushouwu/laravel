<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id')->comment('id');
            $table->string('name')->nullable()->comment('名称');
            $table->string('type')->nullable()->comment('类型');
            $table->jsonb('json')->nullable()->comment('任务');
            $table->string('plan')->nullable()->comment('计划');
            $table->timestamp('datetime')->nullable()->comment('时间');
        });
        DB::statement("ALTER TABLE `tasks` comment '任务' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
