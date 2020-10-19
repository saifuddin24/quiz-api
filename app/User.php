<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isA(){
        return $this->id == 1;
    }

    public function isAdmin(){
        return $this->type()->name == 'admin';
    }

    public function is( $usertypeLabel = ""){
        return $this->type()->name == $usertypeLabel;
    }

    public function  meta( ){
        return $this->hasMany('App\Usermeta', 'user_id', 'id' );
    }

    public function type(){
        return Usertype::find( $this->usertype );
    }

    public function metadata( $group = "settings" ){

        $result = [];
        $metaList = Usermeta::where( ["user_id"=> $this->id, 'group' => $group ] )->get();
         foreach ( $metaList as $m ) {
             $result[ $m->meta_key ] = $m->meta_value;
         }
         return $result;
    }

}
