<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayloadToMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('a_items', function (Blueprint $table) {
            $table->integer('payload')->comment('ペイロード番号')->after('id');
        });

        Schema::table('c_items', function (Blueprint $table) {
            $table->integer('payload')->comment('ペイロード番号')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('a_items', function (Blueprint $table) {
            $table->dropColumn('payload');
        });

        Schema::table('c_items', function (Blueprint $table) {
            $table->dropColumn('payload');
        });
    }
}
