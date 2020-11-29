<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_meta', function (Blueprint $table) {
            $table->id();
            $table->string( 'group',  50 )->nullable()->default(null);
            $table->string( 'meta_name',  191 )->nullable();
            $table->text( 'meta_value' )->nullable()->default(null);
            $table->unsignedBigInteger( 'quest_id');
            $table->foreign( 'quest_id')->references('id')->on('questions')->cascadeOnDelete()->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_meta');
    }
}
