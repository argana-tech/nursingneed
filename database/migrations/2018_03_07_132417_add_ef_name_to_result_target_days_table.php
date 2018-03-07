<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEfNameToResultTargetDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_target_days', function (Blueprint $table) {
            $table
                ->string('ef_name')
                ->nullable()
                ->comment('EF手術名')
                ->after('ef_ward');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_target_days', function (Blueprint $table) {
            $table->dropColumn('ef_name');
        });
    }
}
