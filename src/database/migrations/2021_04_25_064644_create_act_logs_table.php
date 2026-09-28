<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('act_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->string('userName');

            $table->unsignedBigInteger('classObjectId');
            $table->string('classObjectName');

            $table->unsignedBigInteger('teamId')->nullable();
            $table->string('teamName')->nullable();

            $table->smallInteger('authority');
            $table->smallInteger('eventType');
            $table->smallInteger('eventState');
            $table->string('eventTitle');

            $table->timestamps();

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('classObjectId')
                ->references('id')
                ->on('class_objects')
                ->onDelete('cascade');

            $table->foreign('teamId')
                ->references('id')
                ->on('teams')
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
        Schema::dropIfExists('act_logs');
    }
}
