<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('participation') ) {
            Schema::create('participation', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("user_id");
                $table->unsignedInteger("quiz_id");
                $table->timestamp("participation_date");
                $table->json("quiz_data")->nullable()->default(null);

                $table->foreign("user_id")->references("id")->on("users")->cascadeOnDelete();
                $table->foreign("quiz_id")->references("id")->on("quizzes")->onDelete('NO ACTION');
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
        //Schema::dropIfExists('participation');
    }
}
