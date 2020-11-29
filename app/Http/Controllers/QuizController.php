<?php

namespace App\Http\Controllers;

use App\Category;
use App\CatRelation;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\QuizResource;
use App\QuestAssign;
use App\Quiz;
use Illuminate\Http\Request;
use Mockery\Exception;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where =[];
        $quiz = Quiz::withoutTrashed( );

//            dd('DD');
        if( !( $request->user() && $request->user()->isAdmin() ) ) {
            $where['publish'] = 1;
        } else {

            if( $request->get('_trashed' ) == 1 )
                $quiz = Quiz::withTrashed( );

            if( $request->get('_trashed_only' ) == 1 )
                $quiz = Quiz::onlyTrashed( );

            if( $request->get('_published_only' ) == 1 )
                $where['publish'] = 1;
        }

        //dd($where);

        if( $request->has('sort' ) ){
            $orderByType = $request->get('sort_type' ) ?: 'ASC';
            $quiz->orderBy( $request->get('sort'), $orderByType );
        }else {
            $quiz->orderBy('created_at', 'DESC' );
        }

        if( $request->has('search_text' ) ) {
            $filterText = $request->get('search_text' );
            $quiz->where( 'title', 'LIKE', '%' . $filterText . '%');
        }

        $page_size = ( $request->has('page_size') ) ? $request->get( 'page_size' ) : 5;

        QuizResource::wrap('items' );

        //dd( $where, $quiz->where( $where )->toSql());

        if( !$quiz->where( $where )->exists( ) ) {
            return $this->setAndGetResponse("message", "No Quiz Found");
        }

        return QuizResource::collection( $quiz->where( $where )->paginate( $page_size ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
//        dd( request('_assigned_questions') );

        $where =[];

        if( !$this->isAdmin( $request ) )
            $where['publish'] = 1;

        $quiz = Quiz::where( $where );

        if( !$quiz->where('id',$id)->exists() )
            return response([ 'Message' => 'Quiz not found' ], 404);


        return response( ['data' =>  new QuizResource( $quiz->find( $id ) ) ] );


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $id = null)
    {



        $editing =  $request->getMethod() == "PUT";

        $quiz = $editing ? Quiz::find( $id ):new Quiz( );

        if( !$quiz )
            return $this->setAndGetResponse( 'message' , 'Quiz not found!');

        $data = $request->validate([
            'title' => 'required',
            'full_marks' => 'required|numeric',
            'negative_marks_each' => 'numeric',
            'negative_mark_type' => 'in:percent,number',
            'publish' => 'numeric|in:1,2,0',
            'categories' => 'required',
            'answer_options_type' => 'in:ক,A,1,i,১',
            'questions' => 'array',
            'answer_options_sets' => 'array',
            'question_answer' => 'array',
        ]);



        $quiz->answer_options_type = $request->has('answer_options_type') ?
            $data[ 'answer_options_type' ]:'A';

        $quiz->publish = $data[ 'publish' ];
        $quiz->title = $data[ 'title' ];
        $quiz->description = request( 'description' );
        $quiz->user_id = $request->user()->id;
        $quiz->full_marks = $data['full_marks'];
        $quiz->negative_marks_each = request( 'negative_marks_each' )?:0;
        $quiz->negative_mark_type = request( 'negative_mark_type' )? : 'percent';

        $quiz->save( );

        $catResult = Category::addRelation( request('categories'), $quiz->id, 'quiz-subject' );

        $this->set( 'message', $editing ? 'Quiz updated' : 'Quiz added' );
        $this->set( 'action', $editing ? 'updated' : 'added' );
        $this->set( 'success', true );

        QuizResource::$with_assigned_questions = $request->query('_return_with_assigned_questions' ) === 'true';
        $this->set( 'data', new QuizResource( $quiz ) );
        $this->set( 'category', $catResult );

        $data['answer_options_sets'] = isset( $data['answer_options_sets'] )?  $data['answer_options_sets']: [ ];
        $data['question_answers'] = isset( $data['question_answers'] )?  $data['question_answers']: [ ];

        $assignedResult  = [];

        if( isset( $data['questions'] ) && is_array( $data['questions'] ) ) {
            $assignedResult = $quiz->assignQuestions( $data['questions']  )
                ->setAnswerOptionsSets( $data['answer_options_sets'] )
                ->setQuestionAnswers( $data['question_answers'] )
                ->saveAssigned();
        }

        $this->set( 'questions', $assignedResult );

        return  $this->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, $id = null)
    {
        //

        $pDelete = $request->get( '_permanent' ) == "true";
        $bundleDelete = $request->get( '_bundle' ) == "true";
        $Ids = $request->get( '_bundle_ids' );

        $this->set('message', $Ids );

        $Ids = explode( ',',  urldecode( $Ids ) );

        if( !$bundleDelete ) {
            $Ids = [$id];
        }

        $quizzes = $pDelete ? Quiz::whereIn( 'id', $Ids)->onlyTrashed( ) : Quiz::whereIn('id',$Ids)->withoutTrashed( );


//        dd( $quizzes->exists()  );

        if ( $quizzes->exists() ) {
            $deleted = 0;
            $this->set( 'data', $quizzes->get() );

            if( $pDelete ) {
//                if( QuestAssign::where( [ 'quiz_id' => $id ] )->exists() ) {
//                    return $this->setAndGetResponse( 'message', 'Questions assigned with quiz!', 403 );
//                }

                if( $deleted = $quizzes->forceDelete() ) {
                    $this->set('action', 'permanently-deleted');
                }
            }else {
                if( $deleted = $quizzes->delete()) {
                    $this->set('action', 'deleted');
                }
            }

            if( $deleted > 0){
                $this->set( 'count', $deleted );
                return $this->setAndGetResponse('message', "Quiz " . ( $pDelete ? " permanently deleted":"trashed" ) );
            }
        }

        return  $this->setAndGetResponse( 'message' , 'Quiz not found!', 404 );
    }


    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore( Request $request, $id = null)
    {

        $bundleDelete = $request->get( '_bundle' ) == "true";
        $Ids = $request->get( '_bundle_ids' );

        $this->set('message', $Ids );

        $Ids = explode( ',',  urldecode( $Ids ) );

        if( !$bundleDelete ) {
            $Ids = [$id];
        }

        $quizzes = Quiz::whereIn( 'id', $Ids)->onlyTrashed( );

        if ( $quizzes->exists() ) {
            $deleted = 0;
            $this->set( 'data', $quizzes->get() );

            $restored = $quizzes->restore();

            if( $restored > 0 ){
                $this->set( 'count', $restored );
                return $this->setAndGetResponse('message', "Quiz restored" );
            }
        }

        return  $this->setAndGetResponse( 'message' , 'Quiz not found!', 404 );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function take( Request $request){

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_taken(Request $request){

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function questions(Request $request){

    }

}
