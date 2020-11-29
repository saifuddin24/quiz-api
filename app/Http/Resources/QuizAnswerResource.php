<?php

namespace App\Http\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerResource extends JsonResource
{
    public static $withTotalMark = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data= [];

        $data[ "id" ] = $this->id;
        $data[ "participation_id" ] = $this->participation_id;
        $data[ "quest_assign_id" ] = $this->quest_assign_id;
        $data[ "given_answer" ] = $this->given_answer;
        $data[ "right_answer" ] = $this->right_answer;
        $data[ "not_answered" ] = $this->not_answered;
        $data[ "is_correct" ] = $this->is_correct;

        if( self::$withTotalMark ) {
            $data[ "mark_obtained" ] = $this->mark_obtained;
        }

        try{
            $ans_option = \GuzzleHttp\json_decode( $this->answer_options, true );
        }catch ( GuzzleException $exception ) {
            $ans_option = [ ];
        }

        $data[ 'question' ] = isset( $ans_option[ 'question_title'] ) ? $ans_option[ 'question_title']:"";

        $data[ 'options' ] = isset( $ans_option[ 'options'] ) ? $ans_option[ 'options' ] : [ ];


        return $data;

    }
}
