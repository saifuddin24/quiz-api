<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('quest_assign') ) {
            Schema::create('quest_assign', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('quiz_id');
                $table->unsignedBigInteger('question_id');
                $table->text('answer')->nullable()->default(null);
                $table->json('answer_options')->nullable()->default(null);
                $table->integer("position");
                $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
                $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
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
        //Schema::dropIfExists('quest_assign');
    }
}
