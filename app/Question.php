<?php

namespace App;
use App\QuestionMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    //
    use SoftDeletes;

    function meta_list( $group = '' ){
        $m = $this->hasMany('App\QuestionMeta', 'id', 'quest_id' );
        if( $group )
            $m->where('group', $group );
        return $m->get();
    }

    function meta(){
        return $this->hasMany('App\QuestionMeta', 'quest_id', 'id' );
    }

    function save_meta( $meta_name, $value , $group_name = '', $meta_id = 0 ){

        if( is_array($meta_name) ){
            foreach ($meta_name as $meta) {
                if( isset($meta['name']) && isset($meta['value']) ){
                    $this->save_meta( $meta['name'], $meta['value'], isset($meta['group']) ? $meta['group'] : '' );
                }
            }
        }

        $meta_exists = null;
        $meta = QuestionMeta::find( $meta_id ); $meta_exists = $meta;

        if( !$meta ) {
            $meta = QuestionMeta::where( 'quest_id', $this->id )
                ->where( 'group', $group_name )->where( 'meta_name', $meta_name );
            $meta_exists = $meta->exists();
        }

        $meta = $meta_exists ? $meta : new QuestionMeta();


        $meta->quest_id = $this->id;
        $meta->group = $group_name;
        $meta->meta_name = $meta_name;
        $meta->meta_value = $value;

        return $meta->save();
    }

    function category_list( $columns = ['*']){
        return $this->hasMany('App\CatRelation', 'entity_id',  'id')
            ->join('categories', 'categories.id', 'cat_relations.cat_id')->where('type', 'question-subject')
            ->get($columns);
    }

    function category( ){
        return $this->hasMany('App\CatRelation', 'entity_id',  'id')
            ->join('categories', 'categories.id', 'cat_relations.cat_id')->where('type', 'question-subject');
    }

}
