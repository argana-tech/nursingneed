<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHNameToResultTargetDaysTable extends Migration
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
                ->string('h_name')
                ->nullable()
                ->comment('H手術名')
                ->after('h_ward');
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
            $table->dropColumn('h_name');
        });
    }
}
