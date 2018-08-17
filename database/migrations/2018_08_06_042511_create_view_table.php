<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view', function (Blueprint $table) {
            $table->increments('id')->comment('id');
            $table->string('name')->nullable()->comment('名称');
            $table->string('desc')->nullable()->comment('描述');
            $table->string('TABLE_NAME')->nullable()->comment('所属表');
            $table->string('type')->nullable()->comment('类型');//table form view
            $table->jsonb('json')->nullable()->comment('配置');
        });
        DB::statement("ALTER TABLE `view` comment '视图' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view');
    }
}
