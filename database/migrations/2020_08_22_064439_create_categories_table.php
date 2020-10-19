<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('categories', function (Blueprint $table) {
            $table->id( );
            $table->string('name')->nullable();
            $table->text( "description" )->default(NULL);
            $table->string( "type" )->default('quiz-subject' );
            $table->bigInteger('parent')->default(0 );
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
