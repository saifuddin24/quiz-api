<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usertype extends Model
{
    //

    public function users( ){
        return $this->hasMany( 'App\User', 'user_id', 'id' );
    }

    public static function getList(){
        return UserType::pluck( 'id', 'name' );
    }

    public static function getNames(){
        return UserType::pluck( 'name' );
    }

    public static function getIds(){
        return UserType::pluck( 'id' );
    }

    public static function getById($role_id ){
        return self::where( 'id', $role_id )->first( );
    }

    public static function getByName( $role_name ){
        return self::where( 'name', $role_name )->first( );
    }

    public static function getName( $role_id ){
        $data = self::getById( $role_id );
        return $data ? $data->name: null;
    }

    public static function getID( $role_name ){
        $data = self::getByName( $role_name );
        return $data ? $data->id: null;
    }

}
