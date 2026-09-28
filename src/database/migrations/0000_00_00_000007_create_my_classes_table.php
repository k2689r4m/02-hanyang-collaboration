<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('classObjectId');
            $table->integer('version')->default(1);
            $table->json('cardsNum')->nullable();

            $table->boolean('onClassTalk')->default(false);
            $table->boolean('onOrientation')->default(false);
            $table->boolean('onReflectionLog')->default(false);
            $table->boolean('onEvaluation')->default(false);
            $table->boolean('onTeamActivity')->default(false);
            $table->boolean('onProblemAnalysis')->default(false);
            $table->boolean('onTeamAccess')->default(false);
            $table->boolean('onTeamOrientation')->default(false);
            $table->boolean('onTeamTalk')->default(false);
            $table->boolean('onSetting')->default(false);

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
        Schema::dropIfExists('my_classes');
    }
}
