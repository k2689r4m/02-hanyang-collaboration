<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('myPageId')->nullable();
//            $table->unsignedBigInteger('myClassId')->nullable();
            $table->unsignedBigInteger('classObjectId')->nullable();

            $table->unsignedBigInteger('teamId')->nullable();

            $table->smallInteger('type')->default(4);
            $table->string('title');
            $table->smallInteger('apply')->default(0);
            $table->boolean('btnCardTitle')->default(false);
            $table->json('itemsNum')->nullable();
            $table->timestamps();

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('myPageId')
                ->references('id')
                ->on('my_pages')
                ->onDelete('cascade');

//            $table->foreign('myClassId')
//                ->references('id')
//                ->on('my_classes')
//                ->onDelete('cascade');

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
        Schema::dropIfExists('cards');
    }
}
