<?php

namespace App\Http\Controllers;

use App\Category;
use App\CatRelation;
use App\Http\Resources\CategoryResource;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Schema\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Version\Extension\Build;

class CategoriesController extends Controller
{
    /*diff*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(  Request $request, $type = 'quiz-subject' ){


        $tree_levels = $request->get('tree_levels') === 'true';

        $category = Category::where( 'type', $type );

        if( $request->get('disable_parent_sorting' ) !== 'true' ) {
            $category->orderBy('parent', 'ASC');
        }

        $this->apply_sorting( $request, $category );

        if( $request->has('search_text' ) ) {
            $filterText = $request->get('search_text' );
            $category->where( 'name', 'LIKE', '%' . $filterText . '%');
            return response( $category->get( ) );
        }


        $this->_simple_tree( $request, $category->where('parent', 0 )->get(), $type );

        return response( $this->simpleTreeData );
    }

    private function apply_sorting( Request $request, &$category ){
        if( $request->has('sort' ) ){
            $orderByType = $request->get('sort_type' ) ?: 'ASC';
            $category->orderBy( $request->get('sort'), $orderByType );
        }else {
            $category->orderBy( 'name', 'ASC' );
        }
    }

    protected $simpleTreeData = [];
    private $treeList = [];

    private function _tree( $parent = 0, &$list = array( ), $type ){

        foreach ( $list as $index => $item ) {

            $children = Category::where( ['parent'=> $item->id, 'type' => $type ] );


            if( $children->exists() ) {
                $list[ $index ]->children = $children->get( );

                $this->_tree( $item->id, $list[$index]->children, $type  );
            }
        }
    }


    private function _simple_tree( Request $request, $root_list = array( ), $type, $level = 0 ){

        foreach ( $root_list as $index => $item ) {
            $item->level = $level;
            $this->simpleTreeData[] = $item;



            $children = Category::where( ['parent'=> $item->id, 'type' => $type ] );
            $this->apply_sorting( $request, $children );

            if( $children->exists() ) {
                $childrenList = $children->get( );

                $this->_simple_tree( $request, $childrenList, $type, $level + 1  );
            }
        }
    }

    public function tree( $type = 'quiz-subject' ){
        $data =  Category::where( ['parent'=> 0, 'type' => $type] )->get();
        $this->_tree( 0, $data, $type );

        return response($data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, $id = null ){
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save( Request $request, $id = null ) {

        $rdata = [ 'data' => null, 'action' => '', 'success' => false, 'message' => '' ];
        $editing =  $request->getMethod() == "PUT";

        $category = $editing ? Category::findOrFail( $id ):new Category( );

        $uniqueName = Rule::unique('categories' )->where(function ($query) {
            return $query->where( [
                'parent' => request('parent' ),
                'type' => request('type' ) ?:'quiz-subject'
            ]);
        });

        if( $editing ) {
            $uniqueName->ignore( $id );
        }

        $data = $request->validate([
            'name' => [ 'required', $uniqueName ],
            'parent' => 'numeric',
            'type' => 'in:quiz-subject,question-subject,notice,tag',
            'description' => 'max:300',
        ]);

        $category->description = $data[ 'description' ];
        $category->name = $data[ 'name' ];
        $category->parent = $data[ 'parent' ];

        if( !$this->isEdit() ) {
            $category->type = isset( $data[ 'type' ] ) ? $data['type'] : 'quiz-subject';
        }

        $category->save( );

        $rdata[ 'message' ] = $editing ? 'Category updated' : 'Category added';
        $rdata[ 'action' ] = $editing ? 'updated' : 'added';
        $rdata[ 'success' ] = true;
        $rdata[ 'data' ] = $category;


        return  response( $rdata );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id = null ) {

//        $ids = $this->ids( $id );


        if( !Category::find( $id ) )
            return  $this->setAndGetResponse( 'message' , 'This category of id ' . $id . ' is not exists!', 404 );

        if( Category::where( 'parent', $id )->exists( ) )
            return  $this->setAndGetResponse( 'message' , 'This category has children!', 403 );

        if( CatRelation::where( 'cat_id', $id )->exists( ) )
            return  $this->setAndGetResponse( 'message' , 'This category has entities!', 403 );

        $category = Category::findOrFail( $id );
        $this->set( 'data', $category );

        if ( $deleted = $category->delete( ) ) {
            $this->set( 'success', $deleted );
            $this->set( 'count', 1 );
            $this->set( 'action', 'deleted' );
            return  $this->setAndGetResponse( 'message' , 'Category Deleted' );
        }


        return  $this->setAndGetResponse( 'message' , 'Unknown Error!', 405 );


    }



}
