<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });

        Schema::table('a_items', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });

        Schema::table('c_items', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->comment('ユーザーID')->after('id');
        });

        Schema::table('obstetrics_items', function (Blueprint $table) {
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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('a_items', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('c_items', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('obstetrics_items', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
