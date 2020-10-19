<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Nyholm\Psr7\Request;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {

//        dd( $request, $request->user() );
        if( $request->user() && $request->user()->isAdmin() ) {
            return $next( $request );
        }

        if( $request->acceptsJson() ) {
            return response( [ 'message' => 'You are not allowed!' ], 401 );
        } else {
            //redirect( route('admin.login' ) );
        }

    }
}
