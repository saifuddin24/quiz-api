<?php

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Route::prefix( '/user' )->group( function (  ){
    Route::put( '/edit/{id}', 'ApiAuth\UserController@store' );
    Route::get("/list", 'ApiAuth\UserController@index');
});

Route::prefix( '/quiz' )->group( function() {
    Route::post( '/', 'QuizController@save');
    Route::put( '/{id}', 'QuizController@save')->where('id', '^[0-9]+$');;
    Route::delete( '/{id}', 'QuizController@destroy')->where('id', '^[0-9]+$');;
    Route::delete( '/', 'QuizController@destroy');
    Route::patch( '/restore/{id}', 'QuizController@restore');
    Route::patch( '/restore', 'QuizController@restore');


    Route::delete( '/questions', 'QuestionAssignController@destroy' );

    Route::post( '/questions', 'QuestionAssignController@save' );
    Route::put( '/questions', 'QuestionAssignController@save' );
    Route::put( '/questions/{id}', 'QuestionAssignController@save' )->where('id', '^[0-9]+$');
    Route::put( '/questions/hide', 'QuestionAssignController@hide_questions' );
});


Route::prefix( 'category' )->group(function (){
    Route::post( '/', 'CategoriesController@save');
    Route::put( '/{id}', 'CategoriesController@save')->where('id', '^[0-9]+$');;
    Route::delete( '/{id}', 'CategoriesController@destroy')->where('id', '^[0-9]+$');
    Route::delete( '/', 'CategoriesController@destroy')->where('id', '^[0-9]+$');

    Route::get('{type}/entities', 'CategoriesController@entities' );
    Route::post('{type}/entities' , 'CategoriesController@entities');
    Route::delete('/entities/id' , 'CategoriesController@entities' );
});


Route::prefix( '/question' )->group( function( ){
    Route::post('/', 'QuestionController@save');
    Route::delete('/{id}', 'QuestionController@destroy')->where('id', '^[0-9]+$');
    Route::delete('/', 'QuestionController@destroy')->where('id', '^[0-9]+$');
    Route::patch('/restore/{id}', 'QuestionController@restore')->where('id', '^[0-9]+$');
    Route::patch('/restore', 'QuestionController@restore');
    Route::put('/{id}', 'QuestionController@save' )->where('id', '^[0-9]+$');
});
