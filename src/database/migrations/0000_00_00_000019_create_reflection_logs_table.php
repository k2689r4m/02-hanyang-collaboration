<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReflectionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reflection_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cardId')->nullable();
            $table->unsignedBigInteger('itemId');
            $table->unsignedBigInteger('teamId')->nullable();

            $table->string('studentId');
            $table->string('name');
            $table->string('content1');
            $table->string('content2');
            $table->string('content3');
            $table->string('content4');
            $table->string('content5');
            $table->string('content6');
            $table->string('content7');

            $table->timestamps();

            $table->foreign('cardId')
                ->references('id')
                ->on('cards')
                ->onDelete('cascade');

            $table->foreign('itemId')
                ->references('id')
                ->on('items')
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
        Schema::dropIfExists('reflection_logs');
    }
}
