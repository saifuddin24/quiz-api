<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('quizzes') ) {
            Schema::create("quizzes", function (Blueprint $table) {
                $table->increments('id');
                $table->string("title")->default('(untitled)');
                $table->text("description")->default(NULL)->nullable();
                $table->integer("full_marks")->default(100)->nullable();
                $table->float("negative_marks_each", 12, 2)->default(0)->nullable();
                $table->string("negative_mark_type")->default("percent")->nullable();
                $table->string("answer_options_type", 2)->default("A")->nullable();
                $table->unsignedBigInteger("user_id");
                $table->tinyInteger("publish")->nullable()->default(0);
                $table->timestamp("published_at")->useCurrent();
                $table->timestampsTz();
                $table->softDeletesTz();
                $table->foreign("user_id")->references("id")->on("users");
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
        //Schema::dropIfExists('quizzes');
    }
}
