<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    //
    protected $table = "answer_list";
    public $timestamps = null;


    public static function check( $quiz_id, $participation_id, $answer ){
        $quiz = Quiz::find( $quiz_id );

        $total_questions = (int) $quiz->questionRelation( )
            ->where( 'answer_options', '!=', '[]')
            ->orWhere( 'answer_options', ' IS NOT ', 'NULL')->count();

        $full_marks = (int) $quiz->full_marks;

        return $full_marks / $total_questions;
    }



    public static function alreadyGiven( $participation_id, $question_assign_id ){
        return self::where('participation_id', $participation_id)
            ->where( 'quest_assign_id', $question_assign_id )->exists( );
    }


}
