<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');

            $table->string('identification_id')->nullable()->comment('識別ID');
            $table->integer('target_days')->nullable()->comment('対象日数');	//必要?
            $table->integer('unchecked_days')->nullable()->comment('未処理日数');	//必要?
            $table->boolean('is_obstetrics')->nullable()->comment('産科フラグ');
            $table->boolean('is_child')->nullable()->comment('小児フラグ');
            $table->text('remark')->nullable()->comment('備考');

            $table->timestamps();
        });

        Schema::create('result_target_days', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->date('date')->nullable()->comment('対象日付');
            $table->integer('c_master_days')->nullable()->comment('C項目の日数');
            $table->integer('count_days')->nullable()->comment('〇日目');
            $table->string('status')->nullable()->comment('ステータス(集中治療室/データあり/データなし');
            $table->text('remark')->nullable()->comment('備考');
            $table->string('h_ward')->nullable()->comment('Hファイル病棟');
            $table->string('ef_ward')->nullable()->comment('EFファイル病棟');

            $table->timestamps();
        });

        Schema::create('result_in_intensive_ward_days', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->date('date')->nullable()->comment('対象日付');
            $table->string('name')->nullable()->comment('産科名称');

            $table->timestamps();
        });

        Schema::create('result_target_operation_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->date('date')->nullable()->comment('対象日付');
            $table->string('tensu_code')->nullable()->comment('');
            $table->string('densan_code')->nullable()->comment('');
            $table->string('ef_name')->nullable()->comment('');
            $table->string('c_master_name')->nullable()->comment('C項目名称');
            $table->string('c_master_days')->nullable()->comment('C項目日数');
            $table->date('start_date')->nullable()->comment('開始日付');
            $table->date('end_date')->nullable()->comment('終了日付');
            $table->string('remark')->nullable()->comment('備考');
            $table->string('ward')->nullable()->comment('病棟');

            $table->timestamps();
        });

        Schema::create('result_reference_operation_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->date('date')->nullable()->comment('対象日付');
            $table->string('tensu_code')->nullable()->comment('');
            $table->string('densan_code')->nullable()->comment('');
            $table->string('ef_name')->nullable()->comment('');
            $table->date('start_date')->nullable()->comment('開始日付');
            $table->date('end_date')->nullable()->comment('終了日付');
            $table->string('ward')->nullable()->comment('病棟');

            $table->timestamps();
        });

        //必要?
        Schema::create('result_used_h_file_c_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->boolean('payload_check')->nullable()->comment('');
            $table->integer('target_days')->nullable()->comment('対象日数');
            $table->date('date')->nullable()->comment('対象日付');
            $table->string('ward_code')->nullable()->comment('病棟コード');
            $table->string('ward_name')->nullable()->comment('病棟名称');
            $table->string('payload1')->nullable()->comment('ペイロード　対象日数7日');
            $table->string('payload2')->nullable()->comment('ペイロード　対象日数7日');
            $table->string('payload3')->nullable()->comment('ペイロード　対象日数5日');
            $table->string('payload4')->nullable()->comment('ペイロード　対象日数5日');
            $table->string('payload5')->nullable()->comment('ペイロード　対象日数3日');
            $table->string('payload6')->nullable()->comment('ペイロード　対象日数2日');
            $table->string('payload7')->nullable()->comment('ペイロード　対象日数2日');
            $table->text('remark')->nullable()->comment('備考');

            $table->timestamps();
        });

        //必要?
        Schema::create('result_unused_h_file_c_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('result_id')->unsigned()->comment('result id');

            $table->boolean('payload_check')->nullable()->comment('');
            $table->integer('target_days')->nullable()->comment('対象日数');
            $table->date('date')->nullable()->comment('対象日付');
            $table->string('ward_code')->nullable()->comment('病棟コード');
            $table->string('ward_name')->nullable()->comment('病棟名称');
            $table->string('payload1')->nullable()->comment('ペイロード　対象日数7日');
            $table->string('payload2')->nullable()->comment('ペイロード　対象日数7日');
            $table->string('payload3')->nullable()->comment('ペイロード　対象日数5日');
            $table->string('payload4')->nullable()->comment('ペイロード　対象日数5日');
            $table->string('payload5')->nullable()->comment('ペイロード　対象日数3日');
            $table->string('payload6')->nullable()->comment('ペイロード　対象日数2日');
            $table->string('payload7')->nullable()->comment('ペイロード　対象日数2日');
            $table->text('remark')->nullable()->comment('備考');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
        Schema::dropIfExists('result_target_days');
        Schema::dropIfExists('result_in_intensive_ward_days');
        Schema::dropIfExists('result_target_operation_data');
        Schema::dropIfExists('result_reference_operation_data');
        Schema::dropIfExists('result_used_h_file_c_data');
        Schema::dropIfExists('result_unused_h_file_c_data');
    }
}
