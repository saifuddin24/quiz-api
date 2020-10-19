<?php

namespace App\Http\Resources;

use App\Quiz;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{

    static $with_assigned_questions = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'full_marks' => $this->full_marks,
            'negative_marks_each' => $this->negative_marks_each,
            'negative_mark_type' => $this->negative_mark_type,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'answer_options_type' => $this->answer_options_type,
        ];

        $data[ 'category_names' ] = $this->category( )->pluck( 'name' );
        $data[ 'category_ids' ] = $this->category( )->pluck( 'categories.id' );

        if( $request->user() && $request->user()->isAdmin() ) {
            $data[ 'publish' ] = $this->publish;
            $data[ 'deleted_at' ] = $this->deleted_at;
            $data[ 'categories' ] = $this->category_list( );
        }


        if( request('_assigned_questions') === 'true' || self::$with_assigned_questions) {
            QuestionResource::withoutWrapping();
            QuestionResource::$isAssignedList = true;
            $list = $this->questionRelation( );
//            $data[ 'questions' ] = $list->toSql();
            $data[ 'questions' ] = QuestionResource::collection( $list->orderBy( 'position' )->get() );
        }

        return  $data;
    }
}
