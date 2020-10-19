<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Todo as TodoResources;
use App\Http\Resources\UserResource;
use App\User;
use App\Usermeta;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
//        dd( User::all() );

        $user = User::all();

//      dd( $articles->toSql() );
        return UserResource::collection( $user);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveUsermeta( Request $request )
    {
        //
        //{ 'meta-key':'background-color', 'meta-value' : this.settings.bgColor }

        if( $request->user()->id ) {

            $usermeta = Usermeta::where( ['meta_key' => $request->post('meta-key' ), 'user_id' => $request->user()->id  ] )->first() ;

            if( $usermeta == null ) {
                $usermeta = new Usermeta();
            }

            //dd( $usermeta );

            $usermeta->user_id = $request->user()->id;
            $usermeta->meta_key = $request->post("meta-key");
            $usermeta->meta_value = $request->post("meta-value");
            $usermeta->group = "settings";

            if( $usermeta->save( ) ) {
                return response([ 'success' => true, 'message' => 'Settings saved!']);
            }
        }

        return response( ['message' => 'You don\'t have permission for this', 'success' => false] , 403);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        $currentUser = $request->user( );

        $id = $id?: ( $request->user() ? $request->user()->id: '' );

        $user = $request->user( );

        if( $id ) {
            $user = User::find($id);
        }

        return  new UserResource( $user );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
