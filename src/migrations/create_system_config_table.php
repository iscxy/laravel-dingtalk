<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('dingtalk.database.dingtalk_database_table');

        Schema::create($tableNames, function (Blueprint $table) {
            $table->string('group');
            $table->string('key');
            $table->string('value');
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('dingtalk.database.dingtalk_database_table');

        Schema::dropIfExists($tableNames);
    }
}
