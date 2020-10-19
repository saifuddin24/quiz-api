<?php

namespace App\Http\Controllers\DocControllers;

use App\Doc\DocumentRequest;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Documentation extends Controller
{

    private static $FILE_PATH = '../doc/doc-file.json';
    private $requestItems = [],  $requestGroup = [];

    private $groupItems =  [
        "_id", "description",
        "name", "parentId"
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [ 'message' => null, 'documents' => [] ];

        try{
            $file = new Filesystem();
            $file_content = $file->get( base_path( self::$FILE_PATH ) );
        }catch ( FileNotFoundException $exception ) {
            return "Documentation not found!";
        }

        try{
            $documents = \GuzzleHttp\json_decode( $file_content, true );
        }catch (GuzzleException $exception ) {
            $data[ 'message' ] = "Invalid File Format!";
            return "Documentation currently is not available!";
        }

        $docs =& $documents[ 'resources' ];

        //dd( $documents );

        array_walk( $docs, function ( &$req ){

            if( $req['_type']  == 'request' ) {
                $this->requestItems[] = new DocumentRequest( $req );
            } elseif ( $req['_type'] == "request_group" ) {
                $this->requestGroup[] = $this->setRequestGroups($req);
            }
        });


        DocumentRequest::$PARENT_ID = 'wrk_e259450f18674051aa077d92a0ebf767';
        DocumentRequest::$REQUEST_GROUPS = $this->requestGroup;

//        dd( DocumentRequest::findItemById( 'req_42ab8cb29c8d42fe8d150ff6645e86a7' ) );
//        dd( DocumentRequest::listIndex() );
//        dd(  $this->requestGroup , $this->requestItems);
//        dd( DocumentRequest::findListByGroup( [ $data[ 'root_parent' ], 'fld_7a736430d16c4e95abf422b7cc6e7d59' ] ) );


        $data[ 'list_index' ] =  DocumentRequest::listIndex() ;


        return view("doc/view-all" )->with( $data );

    }

    private function setRequestGroups( $groups ){
        $items = [];
        foreach ( $this->groupItems as $item ) {
            if( isset( $groups[ $item ] ) ) {
              $items[ $item ] = $groups[ $item ];
            }
        }
        return $items;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
