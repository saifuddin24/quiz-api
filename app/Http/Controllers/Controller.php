<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $rdata = [ 'data' => null, 'action' => '', 'success' => false, 'message' => '', 'count' => 0 ];


    protected function set( $key, $value ){
        $this->rdata[ $key ] = $value;
    }

    protected function setAndGetResponse( $key, $value , $status = 200, array $headers = [] ){
        $this->rdata[ $key ] = $value;
        return $this->response( $status, $headers );
    }

    protected function response( $status = 200, array $headers = []) {
        return response( $this->rdata,  $status, $headers = [] );
    }

    protected function  isAdmin( &$user_id = null ){
        $user = request()->user();
        if( $user ) {
            $user_id = $user->id;
            return $user->isAdmin();
        }
        return false;
    }

    protected function  user_id(){
        $user = request()->user();
        if( $user ) {
            return $user->id;
        }
        return null;
    }

    protected function isEdit(){
        return request()->getMethod() == 'PUT';
    }

    protected function ids( $singleId = null ){
        $bundleDelete = request()->get( '_bundle' ) == "true";
        $Ids = request()->get( '_bundle_ids' ) ?: '';

        $Ids = is_array($Ids) ? $Ids : explode( ',',  urldecode( $Ids ) );

        if( !$bundleDelete && $singleId ) {
            $Ids = [$singleId];
        }
        return $Ids;
    }

    protected function set_page_option( &$model, PageOption &$options ){

//        dd( request()->get('_trashed' ) );

        if ( $this->isAdmin() ) {
            if( request()->get('_trashed' ) == 1 )
                $model = $model->withTrashed( );

            if( request()->get('_trashed_only' ) == 1 )
                $model = $model->onlyTrashed( );
        }

        if( request()->has('sort' ) ){
            $orderByType = request()->get('sort_type' ) ?: 'ASC';
            $model->orderBy( request()->get('sort' ), $orderByType );
        }else if( $options->default_sort_column ) {
            $model->orderBy ( $options->default_sort_column, 'DESC' );
        }

        if( request()->has('search_text' ) ) {
            $filterText = request()->get('search_text' );
            $model->where( $options->default_sort_column, 'LIKE', '%' . $filterText . '%');
        }

        $options->page_size = ( request()->has('page_size') ) ? request()->get( 'page_size' ) : 5;
        //dd( $options );
    }
}

class PageOption {
    public $search_column;
    public $default_sort_column;
    public $page_size = 10;
    public $where = [];
}
