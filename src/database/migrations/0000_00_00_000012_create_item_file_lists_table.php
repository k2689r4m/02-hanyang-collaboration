<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemFileListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_file_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itemId');

            $table->string('type');
            $table->string('fileName');
            $table->string('pathName');
            $table->string('imgUrl')->nullable();

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
        Schema::dropIfExists('item_file_lists');
    }
}
