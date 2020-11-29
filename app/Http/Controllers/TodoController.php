<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Todo;
use App\Http\Resources\Todo as TodoResources;
use App\ArticleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ){

        $where = [];
        $user = $request->user();

        $where[ 'user_id' ] = $user->id;

        $hideCompleted = $request->get( 'hideCompleted' );


        $articles = Todo::where( $where );

        if( $hideCompleted !== null ) {
            $articles->where( 'completed', 0 );
        }

//      dd( $articles->toSql() );
        return TodoResources::collection( $articles->paginate( $request->get( 'per_page' ) ? : 10 ) );
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
    public function store(Request $requested )
    {
        $todo = new Todo;

        if ( User::find( $requested->user()->id ) ) {

            $todo->user_id = $requested->user( )->id;
            $todo->title = $requested->post('title');
            $todo->description = $requested->post('description');

            if( $todo->save( ) ) {
                $created = Todo::find( $todo->id );
                return new TodoResources( $created );
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
    public function show($id)
    {
        //
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
     * Update the completed status of todo item.
     *
     * @param Request $request
     * @param $id
     * @return TodoResources
     */
    public function make_completed(Request $request, $id)
    {
        $data = [ 'success' => false ];

        TodoResources::wrap( 'todoItem' );


        $todo = Todo::find($id);

        if ( $request->user()->id == $todo->user_id ) {


            $todo->completed = 1;

            if( $todo->save() ) {
                $created = Todo::find( $id );
                $data['success'] = true;
            } else {
                $created = $todo;
            }

            $created->data = $data;
            return new TodoResources( $created );
        }

        return response( ['message' => 'You don\'t have permission for this', 'success' => false] , 403);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, $id )
    {
        $todo = Todo::findOrFail( $id );

        if ( $request->user()->id == $todo->user_id ) {

            $data = [ 'success' => false ];
            if( $todo->delete( ) ) {
                $data['success'] = true;
            }

            $todo->data = $data;
            return new TodoResources( $todo );
        }

        return response( ['message' => 'You don\'t have permission for this', 'success' => false] , 403);

    }
}
