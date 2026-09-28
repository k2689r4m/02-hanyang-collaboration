<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itemId');

            $table->string('semester');
            $table->string('college');
            $table->string('lectureName');
            $table->string('grade');
            $table->string('division');
            $table->string('grades');
            $table->string('professor');
            $table->string('size');
            $table->string('icpblType');
            $table->string('summary');
            $table->string('classGoal');
            $table->string('method');
            $table->string('basicPlan');
            $table->string('title');
            $table->string('role');
            $table->string('scenario');
            $table->json('process');
            $table->string('outputType1');
            $table->string('outputType2');
            $table->string('outputType3');
            $table->string('outputType4');
            $table->string('outputType5');
            $table->string('outputType6');
            $table->string('outputType7');
            $table->string('outputType8')->nullable();
            $table->string('finalOutput');
            $table->string('mainStudent');
            $table->string('sTitle');
            $table->string('sName');
            $table->string('sRole1');
            $table->string('sRole2')->nullable();
            $table->string('sLink');
            $table->string('sFeedback');
            $table->string('sOpinion');
            $table->string('pr1');
            $table->string('pr2');
            $table->string('pr3');
            $table->string('pr4');
            $table->string('pr5');
            $table->string('pr6');

            $table->timestamps();

            $table->foreign('itemId')
                ->references('id')
                ->on('items')
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
        Schema::dropIfExists('operations');
    }
}
