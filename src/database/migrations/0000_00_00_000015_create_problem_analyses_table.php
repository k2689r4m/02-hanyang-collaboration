<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProblemAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_analyses', function (Blueprint $table) {
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
        Schema::dropIfExists('problem_analyses');
    }
}
