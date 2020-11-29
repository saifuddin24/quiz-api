<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('questions') ) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->text('title')->default(NULL)->nullable();
                $table->text('answer')->default(NULL)->nullable();
                $table->text('description')->default(NULL)->nullable();
                $table->unsignedBigInteger("user_id");
                $table->tinyInteger('hidden')->default(1)
                    ->comment('1=hidden, 0=visible');
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
        //Schema::dropIfExists('questions');
    }
}
