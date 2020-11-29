<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswerListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        if( !Schema::hasTable('answer_list') ) {
            Schema::create('answer_list', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('participation_id')->default(NULL);
                $table->unsignedBigInteger('quest_assign_id')->default(NULL);
                $table->text('given_answer')->default(NULL)->nullable();
                $table->text('right_answer')->default(NULL)->nullable();
                $table->json('answer_options')->default(NULL)->nullable();
                $table->foreign("participation_id")->references("id")->on("participation")->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('answer_list');
    }
}
