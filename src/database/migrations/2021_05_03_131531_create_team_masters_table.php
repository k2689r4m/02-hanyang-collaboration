<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_masters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('teamId')->nullable();
            $table->unsignedBigInteger('classObjectId');

            $table->unique(['classObjectId', 'userId']);

            $table->boolean('write')->default(false);

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('teamId')
                ->references('id')
                ->on('teams')
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
        Schema::dropIfExists('team_masters');
    }
}
