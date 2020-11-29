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
        Schema::create("quizzes", function (Blueprint $table) {
            $table->id();
            $table->string( "title" )->default('(untitled)');
            $table->text( "description" )->default(NULL);
            $table->integer("full_marks")->default(100)->nullable();
            $table->float("negative_marks_each", 12, 2)->default(0 )->nullable();
            $table->string("negative_mark_type")->default("percent" )->nullable();
            $table->unsignedBigInteger( "user_id" );
            $table->tinyInteger("publish")->default(0 )->nullable( )->comment( '0=unpublidh,1=publish,2=draft');
            $table->timestampsTz( );
            $table->softDeletesTz( );
            $table->foreign("user_id" )->references("id" )->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}
