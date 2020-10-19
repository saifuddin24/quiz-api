<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1QuestAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quest_assign', function (Blueprint $table) {
            $table->renameColumn( 'options', 'question_data' );
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
            $table->renameColumn( 'question_data', 'options' );
        });
    }
}
