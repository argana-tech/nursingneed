<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_target_days', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_in_intensive_ward_days', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_target_operation_data', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_reference_operation_data', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_used_h_file_c_data', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
        Schema::table('result_unused_h_file_c_data', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_target_days', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_in_intensive_ward_days', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_target_operation_data', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_reference_operation_data', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_used_h_file_c_data', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('result_unused_h_file_c_data', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
