<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestAssign extends Model
{
    //
    protected $table = "quest_assign";
    public $timestamps = false;




    public static function deAssignQuestion( $Ids =[] ){
        $Ids = is_array($Ids) ? $Ids:[$Ids];
        $result = [ 'deleted' => 0, 'data_list' => null, 'message' => ''];
        $questAssign = QuestAssign::whereIn( 'id', $Ids);

//        dd( $questAssign->exists()  );

        if ( $questAssign->exists() ) {
            $result[ 'data_list' ] = $questAssign->get();

            $result['deleted'] = $questAssign->delete();

            if( $result['deleted'] > 0){
                $result["message"] = "Questions deassigned";
                return $result;
            }
        }

        return  $result;
    }
}
