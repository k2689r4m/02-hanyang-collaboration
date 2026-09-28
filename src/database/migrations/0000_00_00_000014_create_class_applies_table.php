<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_applies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itemId')->nullable();
            $table->unsignedBigInteger('classObjectId')->nullable();

            $table->string('code')->nullable();

            $table->string('state')->default('wait');
            $table->string('mode');
            $table->string('type');
            $table->string('grade');
            $table->string('size1');
            $table->integer('size2')->nullable(); // x > 30
            $table->string('proSize1');
            $table->integer('proSize2')->nullable();
            $table->integer('proSize3')->nullable();
            $table->string('department');
            $table->string('daehak');
            $table->string('major');
            $table->boolean('special');
            $table->boolean('special2');
            $table->boolean('special3');
            $table->boolean('special4');
            $table->string('korName');
            $table->string('engName');
            $table->integer('gradesPoint');
            $table->integer('lecturePoint');
            $table->integer('exercisePoint');
            $table->string('description');
            $table->string('meca');
            $table->string('agency')->nullable();
            $table->string('expert')->nullable();
            $table->boolean('role1');
            $table->string('role2')->nullable();
            $table->boolean('role3');
            $table->boolean('role4');
            $table->boolean('role5');
            $table->string('expected1');
            $table->string('expected2');
            $table->string('expected3');
            $table->string('expected4');
            $table->string('expected5');
            $table->string('expected6');
            $table->string('expected7');
            $table->string('expected8')->nullable();
            $table->string('aplName')->nullable();
            $table->string('aplSign')->nullable();
            $table->string('aplOrg')->nullable();
            $table->string('aplTel')->nullable();
            $table->string('aplPhone')->nullable();
            $table->string('aplEmail')->nullable();
            $table->json('applicant');
            $table->string('agree1');

            //==========================================if mode 1
            $table->string('duration')->nullable();
            $table->string('duration2')->nullable();
//            $table->string('conName')->nullable();
//            $table->string('conPer')->nullable();
            $table->json('contribute');
            $table->string('conDescription')->nullable();
            $table->string('agree2')->nullable();
            $table->string('agree3')->nullable();
            //==========================================

            $table->string('basic1');
            $table->string('basic2');
            $table->string('basic3');
            $table->string('basic4');
            $table->string('basic5');
            $table->string('basic6');
            $table->string('basicPlan');

            $table->string('sceContent');
            $table->string('sceGoal');
            $table->string('sceTitle');
            $table->string('sceRole');
            $table->string('sceDetail');

            $table->json('planDetail');

            $table->timestamps();


            $table->foreign('itemId')
                ->references('id')
                ->on('items')
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
        Schema::dropIfExists('class_applies');
    }
}
