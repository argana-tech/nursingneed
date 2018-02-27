<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_items', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable()->comment('名称');
            $table->string('code')->nullable()->comment('コード');
            $table->text('remark')->nullable()->comment('備考');

            $table->timestamps();
        });

        Schema::create('c_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('days')->nullable()->comment('日数');
            $table->string('name')->nullable()->comment('名称');
            $table->string('code')->nullable()->comment('コード');
            $table->text('remark')->nullable()->comment('備考');

            $table->timestamps();
        });

        Schema::create('obstetrics_items', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable()->comment('名称');
            $table->string('code')->nullable()->comment('コード');
            $table->string('kcode')->nullable()->comment('Kコード');
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
        Schema::dropIfExists('a_items');
        Schema::dropIfExists('c_items');
        Schema::dropIfExists('obstetrics_items');
    }
}
