<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class AddContentTypeColumnOnResultTargetDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // C項目または、A項目の区別 用のカラムを result_target_days テーブルに追加
        Schema::table('result_target_days', function (Blueprint $table) {
            $table
                ->string('content_type', 1)
                ->nullable()
                ->comment('C項目または、A項目の区別')
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
            $table->dropColumn('content_type');
        });
    }
}