<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3questAssign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quest_assign', function (Blueprint $table) {
            //
            $table->text('answer' )->default(null)->nullable()->after('question_id');
            $table->dropColumn('right_answer' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quest_assign', function (Blueprint $table) {
            //
        });
    }
}
