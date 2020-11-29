<?php

namespace App\Http\Controllers\ApiAuth;
use App\User;
use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    protected $action = 'reset';


    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        $msg = trans($response);
        if( $response == 'password.change' )
            $msg = 'Password successfully changed';

        $this->set( 'action', $response );
        $this->set( 'success', true );
        return $this->setAndGetResponse('message', $msg );
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse( Request $request, $response )
    {
        $this->set( 'action', $response );
        return $this->setAndGetResponse('message', trans($response), 422 );
    }


    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        //dd('dd' );
        $this->setUserPassword( $user, $password );

        $user->setRememberToken( Str::random( 20 ) );

        $user->save( );

        event(new PasswordReset($user));

        if( $this->action === 'reset' ) {
            $user->delete_tokens( );
        }
        //$this->guard()->login($user);
    }


    protected function changes_credentials(Request $request){
        return $request->only(
            'password', 'password_confirmation', 'token' , 'email'
        );
    }

    protected function passwordCheck($pass){
        if( Auth::check() ) {
            $auth = Auth::user();
            return Hash::check( $pass, $auth->getAuthPassword() );
        }
    }


    protected function changes_rules()
    {
        return [
            'old_password' => ['required'],
            'password' => ['required','confirmed','min:8', function( $attr, $new, $fail ){
                if( $this->passwordCheck($new)) { $fail('You have to provide a new password'); };
            }],
        ];
    }



    public function change( Request $request ){
        if( !Auth::check() ) {
            return $this->sendResetFailedResponse( $request, Password::INVALID_USER );
            $response = PasswordBroker::INVALID_USER;
        }

        $request->validate( [ 'old_password' => [function($attr, $old, $fail ){
            if( !$this->passwordCheck($old)) { $fail('Incorrect password!'); };
        }]], $this->validationErrorMessages() );

        $request->validate( $this->changes_rules(), $this->validationErrorMessages() );

        $credentials = $this->changes_credentials( $request );

        $this->action = 'change';

        $this->resetPassword( Auth::user(), $credentials['password'] );

        return $this->sendResetResponse( $request, 'password.change' );
    }


}
