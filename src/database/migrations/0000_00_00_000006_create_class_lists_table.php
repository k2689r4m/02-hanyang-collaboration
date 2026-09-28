<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId')->nullable();
            $table->unsignedBigInteger('classObjectId');
            $table->smallInteger('permission')->default(1);

            //한양대도 사용하고 일반 유저는 사용할지 안할지?
            $table->string('name');

            //한양대만 사용함
            $table->string('suupTermNm')->nullable();
            $table->string('daehakNm')->nullable();
            $table->string('gwamokNm')->nullable();
            $table->string('hakgwaNm')->nullable();
            $table->string('haksuNo')->nullable();
            $table->string('jeonggonggbnm')->nullable();
            $table->string('suupNo')->nullable();
            $table->string('hpNo')->nullable();
            $table->string('sosokCd')->nullable();
            $table->string('email')->nullable();
            $table->string('hakbun')->nullable();
            $table->string('suupYear')->nullable();
//            $table->string('name')->nullable();
            $table->string('suupTerm')->nullable();
            $table->string('grade')->nullable();
            $table->string('campusCd')->nullable();
            //한양대 끝

            $table->unique(['suupNo', 'hakbun']);

            //당해 당학기 수업에 특정 학번을 가진 학생은 1명
//            $table->unique(['hakbun', 'suupNo', 'suupYear', 'suupTerm']);

            $table->timestamps();

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('classObjectId')
                ->references('id')
                ->on('class_objects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_lists');
    }
}
