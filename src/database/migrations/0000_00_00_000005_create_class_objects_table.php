<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_objects', function (Blueprint $table) {
            $table->id();
//            $table->string('name')->unique();

            //학기
            $table->string('suupTermNm');
            //폐강
            $table->boolean('pyegangYn');
            //수업시간
            $table->datetime('suupTimes');
            //신청 인원
            $table->integer('sincheongInwon');
            //학점
            $table->integer('hakjeom');
            //?학과명
            $table->string('gnjHakgwaNm');
            //이수 점수
            $table->integer('isuGrade');
            //수업 년도
            $table->integer('suupYear');
            //학기(10, 15, 20, 25)
            $table->integer('suupTerm');
            //대표강사 이름
            $table->string('daepyoGangsaNm')->nullable();
            //?소속명
            $table->string('gnjSosokNm');
            //대표강사 전화번호
            $table->string('daepyoGangsaHp')->nullable();
            //대표강사 넘버
            $table->string('daepyoGangsaNo')->nullable();
            //과목 이름
            $table->string('gwamokNm');
            //대표강사 학과
            $table->string('daepyoGangsaHakgwa')->nullable();
            //수업 타입 정보
            $table->string('suupTypeGb');
            //온라인 정보
            $table->boolean('onlineGb');
            //학습 넘버?
            $table->string('haksuNo');
            //수업 넘버
            $table->string('suupNo')->unique();
            //대표강사 대학
            $table->string('daepyoGangsaDaehak')->nullable();
            //?대학명
            $table->string('gnjDaehakNm');
            //교강사
            $table->string('gyogangsa')->nullable();
            //???????????
            $table->string('teuksuSuupInfo');
            //이수 정보명
            $table->string('isuGbNm');
            //대표강사 이메일
            $table->string('daepyoGangsaEmail')->nullable();
            //강의실
            $table->string('ganguiRoom')->nullable();
            //대표강사 직종
            $table->string('daepyoGangsaJikjong')->nullable();
            //캠퍼스 ?????????????????????
            $table->string('campusCd');


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
        Schema::dropIfExists('class_objects');
    }
}
