<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//Alter table 'customers' add column 'fiel_name'
//-name'

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//


Route::get('/app-data', 'AppDataController@index' );


Route::prefix( '/user' )->group( function (  ){

    Route::post( '/login', 'ApiAuth\LoginController@login' )->middleware( 'throttle:20:1' );

    Route::middleware( [ 'auth:api', 'throttle:600,1' ] )->group( function () {
        Route::get('/', 'ApiAuth\UserController@show' );
        Route::post( '/logout', 'ApiAuth\LoginController@logout' )->name("api.logout" );
    });
});

Route::prefix( '/settings' )->group( function (){
    Route::middleware( [ 'auth:api', 'throttle:60,1' ] )->group( function () {
        Route::post( '/save-usermeta', 'ApiAuth\UserController@saveUsermeta' )->name("settings.save-user-meta");
    });
});

Route::prefix( '/quiz' )->group( function(){
    Route::get( '/list', 'QuizController@index');
    Route::get( '/{id}', 'QuizController@show')->where('id', '^[0-9]+$');

    //Quiz Taking/starting

    Route::middleware('auth:api')->group(function () {
        //Taking a quiz by a student
        Route::post( '/take', 'ParticipationController@save' );

        Route::put( '/take', 'ParticipationController@save' ); //Taking a quiz by a student

        Route::delete( '/take', 'ParticipationController@destroy' )
            ->where('id', '^[0-9]+$');  //Bundle Delete of quiz take that taken by student

        Route::delete( '/take/{id}', 'ParticipationController@destroy' )
            ->where('id', '^[0-9]+$');  //Deleting quiz take that taken by student

        Route::get( '/take/list', 'ParticipationController@index' ); //List of quizzes that taken by student

        Route::get( '/take/{id}', 'ParticipationController@show' )
            ->where('id', '^[0-9]+$'); //Single quiz taken by id

        Route::get( '/take/{id}/result', 'ParticipationController@result' )
            ->where('id', '^[0-9]+$');

        //Giving Answer and getting answer list or single answer
        Route::post('{quiz_id}/answer', 'ParticipationController@give_answer');

        Route::get('{quiz_id}/answer/list', 'ParticipationController@answer_list');

        Route::get('{quiz_id}/answer/{id}', 'ParticipationController@get_answer');

        Route::get( '/{id}/questions', 'QuestionAssignController@index' );

    });
});

Route::prefix( '/answer' )->group( function(){
});

Route::prefix( '/category' )->group( function(){
    Route::get( '/{type}/list', 'CategoriesController@index' );
    Route::get( '/{type}/tree', 'CategoriesController@tree' );
    Route::get( '/{id}', 'CategoriesController@show' );
});

Route::prefix( '/question' )->group( function(){
    Route::get('list', 'QuestionController@index');
    Route::get('/{id}', 'QuestionController@show');
});


Route::any( "/{any}", 'NotFoundController@index')->where( "any", ".*" );
