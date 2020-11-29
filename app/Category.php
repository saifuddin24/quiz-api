<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $changes;
    public $timestamps = false;


    public static function entities( $entity_id, $type = '' ){
        return CatRelation::join( 'categories', 'categories.id', 'cat_relations.cat_id' )->where( 'entity_id', $entity_id );
    }

    public function relations() {
        return $this->hasMany(CatRelation::class, 'cat_id', '' )->getResults();
    }

    public static function addRelation( $cat_id, $entity_id, $type = 'quiz-subject' ){

        $ids = $cat_id == null ? [] : ( is_array( $cat_id ) ? $cat_id:[$cat_id] );

        $result = [ 'deleted' => 0, 'inserted' => 0, 'changed' => false  ];



        $result[ 'deleted' ] =  CatRelation::join( 'categories', 'categories.id', 'cat_relations.cat_id')
            ->where( [ "entity_id"=> $entity_id, "categories.type" => $type ] )
            ->whereNotIn( 'cat_relations.cat_id', $ids)
            ->delete();


        foreach ( $ids as $cat_id ) {

            $relation = CatRelation::where(['cat_id' => $cat_id, 'entity_id' => $entity_id]);

            if (!$relation->exists() && Category::where( 'type', $type )->find( $cat_id ) ) {
                $rel = new CatRelation( );
                $rel->cat_id = $cat_id;
                $rel->entity_id = $entity_id;
                $rel->save();
                $result['inserted']++;
            }
        }

        $result['changed'] = $result[ 'deleted' ] > 0 || $result['inserted'] > 0;
        return $result;
    }

}
