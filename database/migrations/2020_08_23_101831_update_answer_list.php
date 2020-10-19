<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAnswerList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answer_list', function (Blueprint $table) {
            //
            $table->text('given_answer' )->nullable()->default(NULL )->change();
            $table->text('right_answer' )->nullable()->default(NULL )->change();
            $table->json('answer_options' )->nullable()->default(NULL )->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
