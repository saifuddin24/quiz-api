<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class Participation extends Model
{
    //
    protected $table = "participation";

    const CREATED_AT = 'participation_date';
    const UPDATED_AT = null;



    function quiz_data( &$exception = [] ) {
        $quiz = json_decode($this->quiz_data);
        if (!$quiz)
            throw new Exception('Quiz Data not found!s', 404);

        return $quiz;
    }

    function questionCount(){
        $quiz = json_decode( $this->quiz_data, true );
        if( $quiz && isset( $quiz[ 'full_mark' ] ) ) {
            return $quiz[ 'full_mark' ];
        }
    }

    function fullMark() {
        $quiz = json_decode( $this->quiz_data, true );
        return ( $quiz && isset( $quiz[ 'full_marks' ] ) ) ? $quiz[ 'full_marks' ] : 0;
    }

    public  function answerCount( ){
        $answer = QuizAnswer::where( "participation_id", $this->id  );
        return $answer->count();
    }


    public  function answerList( ){
        $cols = [
            'id', 'participation_id', 'quest_assign_id', 'given_answer', 'right_answer', 'answer_options',
            DB::raw( 'if(right_answer IS NULL or right_answer = "", 1, 0 ) as not_answered' ),
            DB::raw('if( given_answer IS NOT NULL && given_answer != "" && given_answer = right_answer, 1, 0 ) as is_correct' )
        ];

        return QuizAnswer::where( "participation_id", $this->id  )
            ->get( $cols );
    }

    public function eachQuestionMark( ){
        $quiz = json_decode( $this->quiz_data, true );
//        dd($quiz);
        return ( $quiz && isset($quiz['full_marks']) && isset( $quiz['quest_count'] ) ) ?
            $quiz['full_marks'] / $quiz['quest_count'] : 0;

    }

}
