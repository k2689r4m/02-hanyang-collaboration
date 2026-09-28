<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            //유저 로그인 정보
            $table->string('email')->unique();
            $table->string('password');

            //한양대는 userNm이랑 여기 같게 입력
            $table->string('name');
            //개인 연락처
            $table->string('contact')->nullable();

            //교수만 사용함
            $table->boolean('basicTarget')->default(false);
            $table->boolean('consultingTarget')->default(false);

            //외부 관리자 등 특정 계층 유저만 사용함
            $table->string('code')->unique()->nullable();

            //권한
            $table->smallInteger('authority')->default(1);

            //사용 미정
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            //소셜 정보 ['hanyang', 'kakao', 'naver', null]
            $table->string('social')->nullalbe();

            //소셜만 사용함 (한양대에서도 날아옴)
            $table->string('uuid')->nullable();

            //한양대만 사용
            $table->string('jikwiGb')->nullable();
            $table->boolean('jaejikYn')->nullable();
            $table->boolean('daepyoUserGbYn')->nullable();
            $table->string('daehakNm')->nullable();
            $table->string('jikjongGb')->nullable();
            $table->string('gaeinNo')->nullable();
            $table->string('sinbunGbNm')->nullable();
            $table->string('sosokNm')->nullable();
            $table->integer('iphakYear')->nullable();
            $table->string('userNm')->nullable();
            $table->string('sinbunGb')->nullable();
            $table->string('userGb')->nullable();
            $table->string('sinbunGbEnm')->nullable();
            $table->string('sosokCd')->nullable();
            $table->string('sosokEnm')->nullable();
            $table->string('userGbNm')->nullable();
            $table->string('sosokId')->nullable();
            //한양대 끝

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
        Schema::dropIfExists('users');
    }
}
