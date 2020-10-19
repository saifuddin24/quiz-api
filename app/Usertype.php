<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usertype extends Model
{
    //

    public function users( ){
        return $this->hasMany( 'App\User', 'user_id', 'id' );
    }

}
