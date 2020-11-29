<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AppDataController extends Controller
{

    private $auth, $authenticated = false;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( AuthFactory $auth )
    {
        $this->auth = $auth;
//        $this->middleware('auth:api');
        $this->authenticated = $this->authenticate();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = []; $statusCode = 200;
        $user = Auth::user( );

        $data[ 'authenticated' ] = $this->authenticated;
        $data[ 'user' ] = $user instanceof User ? new  UserResource( $user ) : new \stdClass( );
        $data[ 'is_admin' ] = $user instanceof User ? $user->isAdmin( ) : false;

        $data[ 'metadata' ] = [
            'drawer_open' => false,
            'background_color' => false,
            'app_title' => 'Quiz Circle',
        ];

        return response( $data, $statusCode );
    }


    private function authenticate( $guard = 'api' )
    {

        if ( $checked = $this->auth->guard($guard)->check() ) {
            $this->auth->shouldUse( $guard );
        }

        return $checked;
    }

}
