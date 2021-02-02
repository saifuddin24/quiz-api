<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Participation;
use App\QuestAssign;
use App\Question;
use App\Quiz;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionAssignController extends Controller
{
    //

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request, $quiz_id ){


        $quiz = Quiz::find( $quiz_id );


        if( !$quiz ){
            return $this->setAndGetResponse( 'message', 'Quiz not found!');
        }

        $questions = Quiz::find( $quiz_id )->questionRelation();

        $where = [];

        if( ( !$this->isAdmin() ) ) {
            $questions->where( 'answer_options', '!=', '[]');
            $questions->orWhere( 'answer_options', ' IS NOT ', 'NULL');
        }

        //dd( $questions->toSql() );

        if( !$questions->exists() )
            return $this->setAndGetResponse( 'message', 'No Question found!');


        QuestionResource::$isAssignedList = true;
        QuestionResource::wrap( 'data' );

//        dd( $questions->where( $where )->orderBy('position')->toSql() );

        return QuestionResource::collection( $questions->where( $where )->orderBy('position')->get() );
//        return QuestionResource::collection( $questions->where( $where )->orderBy('position')->get() );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $where =[];

        if( !( $request->user() && $request->user()->isAdmin() ) )
            $where[ 'hidden' ] = 0;

        $questionAssign = Question::where( $where );

        if( !$questionAssign->where( 'id',$id)->exists() )
            return response([ 'Message' => 'Question not found' ], 404);

        return new QuestionResource( $questionAssign->find($id) );
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
        if( $this->isEdit() && $request->get('questions' ) && $request->get('quiz_id' ) ) {

            $data = $request->validate( [
                'quiz_id' => 'required|numeric|exists:quizzes,id',
                'questions' => 'required|array',
                'question_answers' => 'array',
                'answer_options_sets' => 'array',
            ]);

            $data['question_answers'] = isset( $data['question_answers'] )? $data['question_answers']: [ ];
            $data['answer_options_sets'] = isset( $data['answer_options_sets'] )? $data['answer_options_sets']: [ ];

//            dd( $data['answer_options_sets'] );
//            dd($questAssign);

            $this->set( 'success', true );

            $this->set( 'data',
                Quiz::find(  $data[ 'quiz_id' ]  )
                ->assignQuestions( $data['questions']  )
                ->setAnswerOptionsSets( $data['answer_options_sets'] )
                ->setQuestionAnswers( $data['question_answers'] )
                ->saveAssigned( )
            );
            return $this->response();
        }

        $questAssign = $this->isEdit() ? QuestAssign::find( $id ):new QuestAssign( );



        if( !$questAssign )
            return $this->setAndGetResponse( 'message' , 'Undefined assignment id!');

        $uniqueAssign = Rule::unique( 'quest_assign' )->where(function ($query) {
            return $query->where( [ 'question_id' => request('question_id' ) ]);
        });

        $uniqueAssign = !$this->isEdit() ? $uniqueAssign:null;


        $data = $request->validate([
            'quiz_id' => [ Rule::requiredIf( !$this->isEdit()) ,  'exists:quizzes,id', $uniqueAssign ],
            'question_id' => [ Rule::requiredIf( !$this->isEdit() ) , 'numeric', 'exists:questions,id'],
            'answer_options' => 'array'
        ]);

        dd($data);

        if( !$this->isEdit() ) {
            $questAssign->quiz_id = $data[ 'quiz_id'];
            $questAssign->question_id = $data[ 'question_id'];
        }

        if( $request->exists('answer' ) ) {
            $questAssign->answer = request('answer' );
        }else  if( $question = Question::find( $questAssign->question_id )) {
            $questAssign->answer = $question->answer;
        }

        $questAssign->answer_options = isset( $data[ 'answer_options'] ) ? json_encode( $data[ 'answer_options'] ):NULL;

        if( $questAssign->save() ) {
            $this->set( 'data', $questAssign );
            $this->set( 'success', true );
            $this->set( 'message', $this->isEdit() ? 'Assigned question updated' : 'Question assigned' );
            $this->set( 'action', $this->isEdit() ? 'updated' : 'added' );
        }


        return $this->response();
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
        $bundleDelete = $request->get( '_bundle' ) == "true";
        $Ids = $request->get( '_bundle_ids' );

        $Ids = explode( ',',  urldecode( $Ids ) );

        $data = QuestAssign::deAssignQuestion( $Ids );

        $this->set( 'success', $data['deleted'] > 0 );
        $this->set( 'message', $data['message'] );
        $this->set( 'data_list', $data['data_list'] );
        $this->set( 'deleted', $data['deleted'] );

        return  $this->response( );
        //
    }
}
