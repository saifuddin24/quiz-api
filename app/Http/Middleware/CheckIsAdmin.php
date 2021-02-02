<?php

namespace App\Http\Middleware;

use Closure;

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

        if( $request->wantsJson() ) {
            return response( [ 'message' => 'You are not allowed!' ], 401 );
        } else {
            //redirect( route('admin.login' ) );
        }

    }
}
