<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_relations', function ( Blueprint $table ) {
            $table->id( );
            $table->unsignedBigInteger("cat_id")->default(NULL );
            $table->unsignedBigInteger( "entity_id" )->default( NULL );
            $table->integer( "priority" )->default(999 );
            $table->unique( ['cat_id','entity_id' ], 'cat_id-entity_id' );
            $table->foreign('cat_id')->references('id' )->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_relations');
    }
}
