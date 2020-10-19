<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\http\resources\JsonTable as JsonTableResource;
use App\Http\Resources\Todo as TodoResources;
use App\Todo;
use App\JsonTable;



class JsonTableController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $tablename, Request $request)
    {
        //
//        $tablename = $request->get('tablename');
        $data = JsonTable::getTable( $tablename )->getData();
//        $data = $table->getData();

//        JsonTableResource::withoutWrapping();
        return JsonTableResource::make( $data )->response( );

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
    public function store( $tablename, Request $request )
    {

//        $tablename = $request->post('tablename');

        $table = JsonTable::getTable( $tablename );
        $table->setIpuutData( "post", $request );
        $table->addItem(  );

        //return JsonTableResource::make( $res )->response( );
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
