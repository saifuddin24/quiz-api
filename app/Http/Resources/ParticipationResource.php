<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParticipationResource extends JsonResource
{
    public static $answerList = null;
    public static $participationDatails = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];

        $data["id"] = $this->id;
        $data["user_id"] = $this->user_id ;
        $data["quiz_id"] = $this->quiz_id ;
        $data["participation_date"] = $this->participation_date;

        $quest_data = json_decode( $this->quiz_data );

        $quest_data = $quest_data?:new \stdClass();


        $data[ "title" ] = property_exists($quest_data, 'title' ) ? (string) $quest_data->title:"__untitled__";
        $data[ "description" ] = property_exists($quest_data, 'description' ) ? (string) $quest_data->description:null;
        $data[ "full_mark" ] = property_exists($quest_data, 'full_marks' ) ? (int) $quest_data->full_marks:0;
        $data[ "negative_marks_each" ] = property_exists($quest_data, 'negative_marks_each' ) ?
            (float) $quest_data->negative_marks_each:0;
        $data[ "negative_mark_type" ] = property_exists($quest_data, 'negative_mark_type' )
            ? $quest_data->negative_mark_type:null;

        if( self::$participationDatails ) {
            $data[ 'each_question_mark'] = $this->eachQuestMark;
            $data[ 'full_mark' ] = $this->fullMark;
            $data[ 'negative_marks_each' ] = $this->nagetiveMarkEach;
            $data[ 'question_count'] = $this->questionCount;
            $data[ 'answer_count'] = $this->answerCount;
            $data[ 'total_mark_obtained' ] =  $this->totalMarkObtained;
        }
//
//        $data[ 'total_mark_obtained' ] =  44;
//
//        if( property_exists( $this , 'totalMarkObtained' ) ) {
//        }

        if( self::$answerList && self::$answerList instanceof JsonResource ) {
            $data[ "answer_list" ] = self::$answerList;
        }

        return $data;
    }

}
