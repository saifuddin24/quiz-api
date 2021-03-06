<?php

namespace App\Http\Controllers\ApiAuth;
use App\Http\Resources\UserResource;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Input;

class LoginController extends Controller
{

    protected $username = 'phone_number';

    private function getUserId( $user ){
        $user = User::where( 'email', $user  )->orWhere( 'phone_number', $user );
            //->where('deleted_at', 'IS NOT', 'NULL');

        return $user->first() ? $user->first()->id:null;
    }

    //
    public function login( Request $request ){


        $login = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);


        $id = $this->getUserId( $login['username'] );

        //dd($id);

        $loginData = ['id' => $id, 'password' => $login['password'] ];

        $remember = $request->post('remember' ) ? true:false;


        if( !Auth::attempt( $loginData, $remember ) ) {
            return response( [ 'message' => 'login information are incorrect!' ], 403 );
        }

        if( !Auth::user( )->isAdmin( ) && $request->input('ref') == 'admin' ) {
            return response( [ 'message' => 'You are not authenticated!' ], 403 );
        }


        if( $meta = Auth::user()->meta->where( 'meta_key','scopes')->first()) {
            $scopes = (array) json_decode( $meta->meta_value );
        }

        $accessToken = Auth::user()->createToken( "authToken" )->accessToken ?? '';

        return response(
            [
                'user' => new UserResource( Auth::user( ) ),
                'access_token' => $accessToken,
                'message' => "Login successfull",
                'is_admin' => Auth::user( )->isAdmin() ?? false
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
