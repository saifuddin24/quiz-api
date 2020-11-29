<?php

namespace App\Http\Controllers\ApiAuth;
use App\User;
use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response( ['message' => trans($response) ], 200 );

    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse( Request $request, $response )
    {
        return response( [ 'errors' => [trans( $response )] ], 422 );
    }


    //
   /* public function change( Request $request ){


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

        $scopes = [];

        if( $meta = Auth::user()->meta->where( 'meta_key','scopes')->first()) {
            $scopes = (array) json_decode( $meta->meta_value );
        }


        $accessToken = Auth::user()->createToken( "authToken" )->accessToken;

        return response(
            [
                'user' => Auth::user(),
                'access_token' => $accessToken,
                'message' => "Login successfull"
            ]
        );
    }*/

}
