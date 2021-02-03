<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    public static $relatedQuestColumns = [
        "quest_assign.id as assigned_id", "quiz_id", "question_id", "answer_options", "position",
        "title", "questions.answer", "quest_assign.answer as assigned_answer", "description", "user_id", "deleted_at"
    ];

    private $questionIds = [], $quiz_id, $answer_options_sets = [], $questionAnswers = [];

    public function questionRelation( ){
        return Question::join('quest_assign', 'questions.id', '=', 'quest_assign.question_id' )
            ->where( 'quiz_id', $this->id )->select( self::$relatedQuestColumns );
    }

    public function hasQuestions(){
        return $this->questionRelation()->exists();
    }


    function category_list( $columns = ['*']){
        return $this->hasMany('App\CatRelation', 'entity_id',  'id')
            ->join('categories', 'categories.id', 'cat_relations.cat_id')->where('type', 'quiz-subject')
            ->get($columns);
    }

    function category( ){
        return $this->hasMany('App\CatRelation', 'entity_id',  'id')
            ->join('categories', 'categories.id', 'cat_relations.cat_id')->where('type', 'quiz-subject');
    }

    public function assignQuestions( $quiz_id = null, $questions = null, $questionsData = []){
        if( is_array($quiz_id) ) {
            $questionsData = $questions;
            $questions = $quiz_id;
            $quiz_id = $this->id;
        }

        $this->quiz_id = $quiz_id;
        $this->questionIds =  is_array( $questions ) ? $questions: [ $questions ];
        $this->answer_options_sets = $questionsData;
        return $this;
    }


    public function setAnswerOptionsSets($answer_options_sets ){
        $this->answer_options_sets = $answer_options_sets;
        return $this;
    }

    public function setQuestionAnswers( $questionsAnswers ){
        $this->questionAnswers = $questionsAnswers;
        return $this;
    }

    public function saveAssigned( )
    {
        $result = [ 'deleted' => 0, 'updated' => 0, 'inserted' => 0, 'total' => 0,
            'inserted_data' => [],
            'updated_data' => []
        ];

        $result[ 'deleted' ] =  QuestAssign::where( [ "quiz_id" => $this->quiz_id ] )
            ->whereNotIn( 'question_id', $this->questionIds )
            ->delete();

        $position = 0;

       foreach ( $this->questionIds as $question_id ) {

            if( Question::find( $question_id ) ) {
                $relation = QuestAssign::where( [ 'question_id' => $question_id, 'quiz_id' => $this->quiz_id ] );
                $quid_in = 'qid_' . $question_id;
                $answer_options_set =  isset( $this->answer_options_sets[ $quid_in ] )
                    ? $this->answer_options_sets[ $quid_in ] : null;

                $exists = $relation->exists( );

                $assignment = $exists ? QuestAssign::find( $relation->first()->id ):new QuestAssign( );

                $assignment->question_id = $question_id;
                $assignment->quiz_id = $this->quiz_id;

                if( isset( $this->questionAnswers[ $quid_in ] ) ) {
                    $assignment->answer = $this->questionAnswers[ $quid_in ];
                }else  if( $question = Question::find( $question_id )) {
                    $assignment->answer = $question->answer;
                }
                $assignment->answer_options =  !empty( $answer_options_set ) ? json_encode( $answer_options_set ) : $assignment->answer_options;
                $assignment->position =  ++$position;
                $saved = $assignment->save( );


//                if( !$exists ){
//                    $result['inserted_data'][] = $assignment;
//                }else {
//
//                }


                $result['updated_data'][] = $assignment;

                if( $saved ) {
                    if( $assignment->wasRecentlyCreated ) {
                        $result[ 'inserted' ]++;
                    }else {
                        $result[ 'updated' ]++;
                    }
                }

            }

        }


        $result['total'] =  QuestAssign::where( [ 'quiz_id' => $this->quiz_id ] )->count();

        return $result;
    }

    public  function questionCount( ){

        return (int) $this->questionRelation( )
            ->where( 'answer_options', '!=', '[]')
            ->orWhereNotNull( 'answer_options' )->count();
    }

    public  function markEach( ){

        $total_questions = (int) $this->questionRelation( )
            ->where( 'answer_options', '!=', '[]')
            ->orWhere( 'answer_options', ' IS NOT ', 'NULL')->count();

        $full_marks = (int) $this->full_marks;

        return $full_marks / $total_questions;
    }

}
