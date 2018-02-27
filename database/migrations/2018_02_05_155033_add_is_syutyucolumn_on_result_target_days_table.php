<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class AddIsSyutyuColumnOnResultTargetDaysTable extends Migration
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
                ->boolean('is_syutyu')
                ->nullable()
                ->comment('集中治療室')
                ->after('content_type');
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
            $table->dropColumn('is_syutyu');
        });
    }
}