<?php

namespace App\Http\Resources;

use App\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public static $isAssignedList = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        $data = [];

        if( self::$isAssignedList ) {
            $category = Question::find( $this->question_id )->category( );
            $data['assigned_id'] = $this->assigned_id;
            $data['quiz_id'] = $this->quiz_id;
            $data['question_id'] = $this->question_id;
            $data['position'] = $this->position;
            $data['answer_options'] = $this->answer_options == null ? null : json_decode( $this->answer_options );
            $data['assigned_answer'] = $this->assigned_answer;
        }else {
            $category = $this->category( );
            $data['id'] = $this->id;
            $data['user_id'] = $this->user_id;
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
        }


        $data[ 'category_ids' ] = $category ? $category->pluck( 'categories.id' ) : [];
        $data[ 'category_names' ] = $category ? $category->pluck( 'name' ) : [];
        $data[ 'category_ids' ] = $category ? $category->pluck( 'categories.id' ) : [];



        $data[ 'title'] = $this->title;
        $data[ 'answer'] = $this->answer;
        $data[ 'description'] = $this->description;

        if( $request->user() && $request->user()->isAdmin() ) {
            $data[ 'hidden' ] = $this->hidden;
            $data[ 'deleted_at' ] = $this->deleted_at;
            $data[ 'categories' ] = $this->category_list( );
            $data[ 'meta_list' ] = $this->meta( )->where('group', 'opts')->get(['id','meta_name','meta_value']);
        }

        return $data;
    }
}
