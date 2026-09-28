<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basics', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('consultantId');
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
            $table->string('title');
//            $table->unsignedSmallInteger('maxMemberCount');
            $table->timestamps();

//            $table->foreign('consultantId')
//                ->references('id')
//                ->on('users')
//                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basics');
    }
}
