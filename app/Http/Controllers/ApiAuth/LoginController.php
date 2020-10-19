<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login(Request $request){


        $login = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $remember = $request->post('remember' ) ? true:false;


        if( !Auth::attempt( $login, $remember ) ) {
            return response( [ 'message' => 'login information are incorrect!' ], 403 );
        }

        $scopes = [];

        if( $meta = Auth::user()->meta->where( 'meta_key','scopes')->first()) {
            $scopes = (array) json_decode( $meta->meta_value );
        }


        $accessToken = Auth::user()->createToken( "authToken")->accessToken;

        return response(
            [
                'user' => Auth::user(),
                'access_token' => $accessToken,
                'message' => "Login successfull"
            ]
        );
    }

    /**
     * Loggin out the user via api
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout( Request $request ){
        $currentUser = $request->user( );
        $currentUser->tokens()->each(function ($token, $key) {
            $token->delete();
        });

        return response(['message' => 'Successfully logged out', 'success' => true]);

    }
}
