<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\ParticipationResource;
use App\Http\Resources\QuizAnswerResource;
use App\Http\Resources\QuizResource;
use App\Participation;
use App\Http\Resources\QuestionResource;
use App\QuestAssign;
use App\Question;
use App\Quiz;
use App\QuizAnswer;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Psr\Log\InvalidArgumentException;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where =[];

        $participation = new Participation;

        if( !$this->isAdmin( ) ) {
//            dd($this->user_id());
            $where[ 'participation.user_id' ] = $this->user_id();
        }


        if( !$participation->where( $where )->exists() )
            return $this->setAndGetResponse( 'message', 'No Participation Found', 404);

        ParticipationResource::withoutWrapping();


        return ParticipationResource::collection( $participation->where( $where )->get() );
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

        if( !$this->isAdmin() )
            $where[ 'user_id' ] = $this->user_id();

        $participation = Participation::where( $where );

        if( !$participation->where( 'id',$id)->exists() )
            return response([ 'Message' => 'Participation not found' ], 404);

        return new ParticipationResource( $participation->find($id) );
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
        $participation = $this->isEdit() ? Participation::find( $id ):new Participation( );

        if( !$participation )
            return $this->setAndGetResponse( 'message' , 'Not found!');

        $quizQuestionsValid = Rule::exists('quest_assign' )->where( function(&$query) {
            $query->where( 'question_data','!=', "[]" )->orWhere( 'question_data','IS NOT', NULL );
        });

        if( !$this->isEdit() ) {

            $data = $request->validate([
                'user_id' => 'required|numeric|exists:users,id',
                'quiz_id' => [ 'required','numeric','exists:quizzes,id','exists:quest_assign', $quizQuestionsValid],
            ]);

        }else {
            return (['message' => 'editing, currently is not allowed!']);
        }

        $participation->user_id = $data[ 'user_id' ];
        $participation->quiz_id = $data[ 'quiz_id' ];

        $quiz = Quiz::select([
            "id",
            "title",
            "description",
            "full_marks",
            "negative_marks_each",
            "negative_mark_type"
        ])->find( $participation->quiz_id );

        //dd( $quiz->id, json_encode( $quiz ) );

        $quiz->quest_count = $quiz->question_count( );
        $participation->quiz_data = json_encode( $quiz );

        //dd($participation);

        if( $participation->save( ) ) {

            $quiz->select( '' );

            $relations = $quiz->questionRelation()
                ->where( 'question_data', '!=', '[]')
                ->orWhere( 'question_data', ' IS NOT ', 'NULL')->get( );

            $this->set( 'quiz',  new QuizResource( $quiz ) );
            $this->set( 'questions' , $relations );


        }



        $this->set( 'message', $this->isEdit() ? 'Pertication updated' : 'Pertication added' );
        $this->set( 'action', $this->isEdit() ? 'updated' : 'added' );
        $this->set( 'success', true );
        $this->set( 'data', $participation );

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

        $Ids = $this->ids( $id );

        $participation = Participation::whereIn('id',$Ids);

        if( !$this->isAdmin() )
            $participation->where( 'user_id', $this->user_id() );


        if ( !$participation->exists() )
            return  $this->setAndGetResponse( 'message' , 'Participation not found!', 404 );

        $deleted = 0;
        $this->set( 'data', $participation->get() );

        if( $deleted = $participation->delete()) {
            $this->set('action', 'deleted');
        }

        if( $deleted > 0){
            $this->set( 'delete_count', $deleted );
            return $this->setAndGetResponse('message', "Participation deleted" );
        }

        return  $this->setAndGetResponse( 'message' , 'No Action Done!', 204 );

    }

    public function result(Request $request, $id ){

        $participation = Participation::find( $id );

        if( !$participation )
            return $this->setAndGetResponse( 'message', 'Participation not found!', 404 );


        if( $this->user_id() !== $participation->user_id )
            return $this->setAndGetResponse( 'message', 'You are not allowed!', 403 );

        try {
            $quiz = $participation->quiz_data( );
        } catch ( Exception $e ){
            return $this->setAndGetResponse( "message", $e->getMessage(), $e->getCode() );
        }



        $ngMarkEach = property_exists($quiz, 'negative_marks_each') ? $quiz->negative_marks_each : 0;
        $ngMarkType = property_exists($quiz, 'negative_mark_type') ? $quiz->negative_mark_type : "percent";
        $eachQuestMark = $participation->eachQuestionMark( );
        $fullMark = $participation->fullMark( );
        $questionCount = $participation->questionCount( );
        $answerCount = $participation->answerCount( );
        $answerList = $participation->answerList( );

        $participation->eachQuestMark = $eachQuestMark;
        $participation->fullMark = $fullMark;
        $participation->nagetiveMarkEach = $ngMarkEach;
        $participation->nagetiveMarkType = $ngMarkType;
        $participation->answerCount = $answerCount;
        $participation->questionCount = $questionCount;
        $participation->totalMarkObtained = 0;

        foreach ( $answerList as $in => &$item ) {

            if( $ngMarkEach > 0 && !empty( $item->given_answer) && $item->is_correct == 0  ) {

                switch ( $ngMarkType ) {
                    case "percent":
                        $item->mark_obtained -= $eachQuestMark * ( $ngMarkEach / 100 );
                        break;
                    default:
                        $item->mark_obtained -= $eachQuestMark;
                }
            }else {
                $item->mark_obtained = $item->is_correct == 1 ? $eachQuestMark : 0;
            }

            $participation->totalMarkObtained += $item->mark_obtained;
        }

        QuizAnswerResource::$withTotalMark = true;
        ParticipationResource::$participationDatails = true;
        ParticipationResource::$answerList = QuizAnswerResource::collection( $answerList );

        return new ParticipationResource( $participation ) ;

    }

    public function get_answer(){

    }

    public function give_answer( Request $request, $quiz_id ){

        $quiz = Quiz::find(  $quiz_id );

        if( !$quiz->exists() )
            return $this->setAndGetResponse('message', 'Quiz is not exists!');

        if( isset( Quiz::$relatedQuestColumns["questions.answer"]) )
            unset( Quiz::$relatedQuestColumns["questions.answer"] );

        Quiz::$relatedQuestColumns[] = "questions.answer";
        Quiz::$relatedQuestColumns[] = "quest_assign.answer as assigned_answer";

        $participationExists = Rule::exists('participation', 'id' )->where('quiz_id', $quiz_id );
        $questionAssigned = Rule::exists('quest_assign', 'question_id' )->where('quiz_id', $quiz_id );



        $data = $request->validate([
            'participation_id' => ['required' , 'numeric', $participationExists],
            'question_id' => ['required','numeric', $questionAssigned ],
        ]);



        $question = $quiz->questionRelation()
            ->where( 'question_id', $data['question_id'] )->first( );


        $qu_cols = [
            "participation.user_id", "quizzes.id", "quiz_id", "participation_date", "title",
            "description","full_marks","negative_marks_each","negative_mark_type"
        ];

        $participation = Participation::join( 'quizzes', 'quizzes.id', 'participation.quiz_id')->find( $data[ "participation_id" ] , $qu_cols );

//        return response( [$question] );

        if( !$participation )
            return $this->setAndGetResponse( 'message' , 'Not found!', 404 );

        if( $participation->user_id !== $this->user_id() )
            return $this->setAndGetResponse( 'message' , 'You are not allowed', 403 );

        if( QuizAnswer::alreadyGiven( $data[ "participation_id" ], $question->assigned_id ) )
            return $this->setAndGetResponse( 'message', 'Already given the answer!', 403);


        $answer = new QuizAnswer();

        $qData = json_decode( $question->question_data, true );
        $qData = is_array($qData) ? $qData:[];

        $qData['question_title'] = $question->title;

        $answer->participation_id   = $data[ "participation_id" ];
        $answer->quest_assign_id    = $question->assigned_id;
        $answer->given_answer       = request("answer") ?: NULL;
        $answer->right_answer       = $question->answer;
        $answer->answer_options     = json_encode( $qData );

        if(  $answer->save() ) {
            $this->set( 'success', true );
            $this->set( 'data', $answer );
            $this->set( 'action', 'answer_given' );
            $this->set( 'message', 'successfully given the answer' );
        }

        return $this->response( );

    }

}
