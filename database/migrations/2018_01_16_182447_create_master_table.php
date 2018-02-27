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

            $table->string('name')->nullable()->comment('����');
            $table->string('code')->nullable()->comment('�R�[�h');
            $table->text('remark')->nullable()->comment('���l');

            $table->timestamps();
        });

        Schema::create('c_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('days')->nullable()->comment('����');
            $table->string('name')->nullable()->comment('����');
            $table->string('code')->nullable()->comment('�R�[�h');
            $table->text('remark')->nullable()->comment('���l');

            $table->timestamps();
        });

        Schema::create('obstetrics_items', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable()->comment('����');
            $table->string('code')->nullable()->comment('�R�[�h');
            $table->string('kcode')->nullable()->comment('K�R�[�h');
            $table->text('remark')->nullable()->comment('���l');

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
