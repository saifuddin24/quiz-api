<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable("users" ) ) {

            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string( 'first_name', 100)->nullable( );
                $table->string( 'last_name', 100)->nullable( );
                $table->string( 'display_name', 200)->nullable( );
                $table->string( 'email' )->unique( )->nullable( );
                $table->string( 'phone_number' )->unique( )->nullable( );
                $table->string( 'password' )->nullable( );
                $table->string( 'profile_pic' )->nullable( );
                $table->enum(   'user_from', [ '', 'google', 'facebook' ] )->nullable()->default('');
                $table->enum(   'gender', [ '', '0', '1' ] )->nullable()->default('');
                $table->string( 'social_user_id', 100 )->nullable()->default(NULL);
                $table->string( 'lang', 8 );
                $table->integer('usertype' );
                $table->ipAddress("ip_address" )->default(NULL );
                $table->rememberToken(  );
                $table->timestampTz('email_verified_at' )->nullable( );
                $table->timestampsTz(  );
                $table->softDeletesTz( );

                $table->foreign('usertype')->references('id')->on('usertypes')
                    ->onUpdate("CASCADE" )
                    ->onDelete( "RESTRICT" );
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
        Schema::dropIfExists('users');
    }
}
