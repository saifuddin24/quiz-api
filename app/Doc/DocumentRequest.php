<?php

namespace App\Doc;


class DocumentRequest
{

    public static $BASE_URL = "";
    public static $PARENT_ID = "";

    public static $REQUEST_GROUPS = [];

    public $id, $title, $headers, $body, $url, $queries, $method , $description, $parent;

    private $headerAttrs = [ 'name', 'value' ];
    private $queryAttrs = [ 'name', 'description' ];
    private $bodyAttrs = [ 'mimeType', 'params', 'text' ];
    private $bodyOthers = [ 'mimeType' => 'text' ];

    private static $requestList = [];

    //
    public function __construct(  $data = null )
    {

        $data = is_object( $data ) ? $data: (object) $data;

        self::$BASE_URL = url("");

//        $this->title = $data;

        if( $data ) {
            $this->id = property_exists($data , "_id") ? $data->_id:null;
            $this->title = property_exists($data , "name") ? $data->name:null;
            $this->body = property_exists($data , "body") ? $data->body:[];
            $this->headers = property_exists($data , "headers") ? $data->headers:[];
            $this->description = property_exists($data , "description") ? $data->description:null;
            $this->method = property_exists($data , "method") ? $data->method:null;
            $this->url = property_exists($data , "url") ? $data->url:null;
            $this->queries = property_exists( $data , "parameters" ) ? $data->parameters:null;
            $this->parent = property_exists( $data , "parentId" ) ? $data->parentId:null;

            $this->setUrl();
            $this->setHeaders();
            $this->setBody();
            $this->setQuery();

            self::$requestList[] = $this;
        }
    }

    private static $pid;

    public static function indexKey( $title ){
        return strtolower( preg_replace( "/[ |_]/", '-', $title ) );
    }

    public static function listIndex(){
        $result = [];

        $roots = self::findListByGroup( self::$PARENT_ID );

        foreach ( $roots as $root ) {
            $result[ self::indexKey( $root->title )] = [
                'title' => ucwords($root->title),
                'id' =>  $root->id,
                'parents' => [],
                'is_group_title' => false,
            ];
        }

        if(is_array( self::$REQUEST_GROUPS )) {

            foreach (self::$REQUEST_GROUPS as $GROUP ) {

                $result[ self::indexKey( $GROUP["name"] )  ] = [
                    'title' => ucwords( $GROUP["name"] ),
                    'id' =>  $GROUP["_id"],
                    'parents' => [],
                    'is_group_title' => true,
                ];

                $subList = self::findListByGroup( $GROUP["_id"] );

//                $result[  self::indexKey(  $root->title ) ] = ucwords($root->title);

                foreach ( $subList as $sub ) {
                    $result[ self::indexKey( $GROUP["name"] )  ][ 'parents' ][] = [
                        'title' => ucwords( $sub->title ),
                        'id' =>  $sub->id,
                        'parents' => [],
                        'is_group_title' => false,
                    ];
                }
            }

        }

        return $result;

    }

    private static $_id;

    public static function findItemById( $id ){
        self::$_id = $id;

        $arrs = array_filter( self::$requestList, function ( $item ){
            if( $item->id == self::$_id ) return $item;
        });

        $keys = array_keys( $arrs );
        return isset($keys[0] ) ? $arrs[$keys[0]]:[];

    }


    public static function findListByGroup( $parentId ){
        self::$pid = $parentId;

        return array_filter( self::$requestList, function ( $item ){

            if( is_array(self::$pid) &&  in_array($item->parent , self::$pid )  ) {
                return $item;
            }
            if( $item->parent == self::$pid ) return $item;

        });
    }


    private function setUrl(){
        $pf = "___";
        self::$BASE_URL = $pf . preg_replace( "/^[http|https]+\:\/\//", "" , self::$BASE_URL );
        $this->url = $pf . preg_replace( "/^[http|https]+\:\/\//", "" , $this->url );

        $this->url = str_replace( self::$BASE_URL, "", $this->url );
    }

    private function reassignValues( &$data = [], $attrs){
        $res = [];
        foreach ($attrs as $key ) {
            if( isset( $data[ $key ] ) ) $res[$key] = $data[ $key ];
        }
        $data = $res;
    }

    private function setHeaders(){
        foreach ( $this->headers as &$header ) {
            $this->reassignValues($header, $this->headerAttrs );
        }

    }

    private function setBody(){
        if( !is_array( $this->body ) || empty( $this->body ) ) return;

        $this->reassignValues($this->body, $this->bodyAttrs );

        foreach ( $this->bodyOthers as $typeKey => $key ) {

//            $this->body[ $typeKey ] = $key;
            if( isset( $this->body[$typeKey] ) ) {
                switch ( $this->body[$typeKey] ) {
                    case "application/json":
                        $this->body[ 'params' ] = isset( $this->body[$key] ) ? json_decode( $this->body[$key], true):['d'];

                        break;
                }
            }
        }


    }

    private function setQuery(){
        foreach ($this->queries as $query ) {
            $this->reassignValues( $query, $this->queryAttrs );
        }
    }

}
