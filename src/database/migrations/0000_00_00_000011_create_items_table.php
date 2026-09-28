<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cardId');

            $table->unsignedBigInteger('basicApplyId')->nullable();
            $table->unsignedBigInteger('consultingApplyId')->nullable();

            $table->string('title');
            $table->string('content')->nullable();
            $table->json('images')->nullable();
            $table->json('files')->nullable();
            $table->json('comments')->nullable();
            $table->datetime('deadLine')->nullable();
            $table->json('party')->nullable();
            $table->string('label')->default('0');
            $table->json('checks')->nullable();

            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('type')->default(0);

            $table->boolean('activationDeadline')->default(false);
            $table->boolean('activationImage')->default(true);
            $table->boolean('activationParty')->default(false);
            $table->boolean('activationLabel')->default(false);
            $table->boolean('activationCheck')->default(false);
            $table->boolean('activationFile')->default(false);

            $table->json('brain')->nullable();
//            $table->json('classApply')->nullable();
//            $table->json('detaileOperApply')->nullable();
//            $table->json('problemAnalysis')->nullable();
//            $table->json('teamActivity')->nullable();
//            $table->json('evaluation')->nullable();
//            $table->json('reflectionLog')->nullable();
//            $table->json('operationResult')->nullable();

            $table->timestamps();

            $table->foreign('cardId')
                ->references('id')
                ->on('cards')
                ->onDelete('cascade');

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('basicApplyId')
                ->references('id')
                ->on('basicApplies')
                ->onDelete('cascade');

            $table->foreign('consultingApplyId')
                ->references('id')
                ->on('consultingApplies')
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
        Schema::dropIfExists('items');
    }
}
