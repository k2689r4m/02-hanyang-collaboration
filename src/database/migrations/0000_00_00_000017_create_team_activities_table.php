<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cardId')->nullable();
            $table->unsignedBigInteger('itemId');
            $table->unsignedBigInteger('teamId')->nullable();

            $table->string('dateTime');
            $table->string('problemSolvingProcess');
            $table->string('attendees');
            $table->string('mainActivities');
            $table->string('task1');
            $table->string('task2');
            $table->string('discuss1');
            $table->string('schedule1');
            $table->string('schedule2');
            $table->string('schedule3');
            $table->string('schedule4');
            $table->string('feedback')->nullable();

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
        Schema::dropIfExists('team_activities');
    }
}
