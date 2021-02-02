<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\QuestionResource;
use App\QuestAssign;
use App\Question;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $quiz = Question::query();


        $page_options = new PageOption();
        $where =[];

        $page_options->default_sort_column = 'title';
        $page_options->search_column = 'title';
        $page_options->where = $where;

        $this->set_page_option( $quiz, $page_options );

        $where[ 'hidden' ] = 0;

        if( ( $this->isAdmin($user_id) ) ) {
            if( $request->get('_hidden_only' ) == 1 )
                $where['hidden'] = 1;
            else unset($where['hidden']);
        }

        if( !$quiz->where( $where )->exists() )
            return response(["message" => "No Question Found"]);

        if ( $request->has('exclution' ) ) {
            $exclutionList = explode( ',',  $request->get('exclution' ) );
            $quiz->whereNotIn( 'id', $exclutionList );
        }

        $data = QuestionResource::collection( $quiz->where( $where )->paginate( $page_options->page_size ) );
        $data->additional( [ 'sql' => $quiz->toSql() ]);

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $where =[];

        if( !( $request->user() && $request->user()->isAdmin() ) )
            $where[ 'hidden' ] = 0;

        $question = Question::where( $where );

        if( !$question->where( 'id',$id)->exists() )
            return response([ 'Message' => 'Question not found' ], 404);

        return new QuestionResource( $question->find($id) );

    }

    public function saveBundle( Request $request ){
        if( request('questions' ) && is_array(request('questions') ) ) {

            $qList = request( 'questions' );
            $result = [];

            foreach ( $qList as $index => $item ) {
                $result[$index]  = [ 'edit' => false, 'message' => '', 'success' => false, 'data' => null, 'category' =>[] ];

                if( !isset( $item['title'] ) && empty( $item['title'] ) ){
                    $result[$index]['message'] = 'title is not defined';
                    continue;
                }

                $cats = is_array( $item['categories'] ) ?$item['categories']:  [ $item['categories'] ];

                if(empty($cats)){
                    $result[$index]['message'] = 'category is not defined for title ' . $item['title'];
                    continue;
                }

                $quest = Question::find( isset( $item[ "id" ] ) ? $item[ "id" ]:null);
                $edit = (boolean) $quest;
                $quest = $quest?:new Question();
                $result[$index][ "edit" ] =  $edit;

                $qu = Question::where( 'title', $item[ "title" ] );
                if( $edit ) $qu->where( 'id', '!=', $item['id'] );

                if ( $qu->exists() ) {
                    $result[$index][ "message" ] =  $item[ "title" ] . 'Already exists!';
                }

                $quest->user_id = request()->user()->id;

                $quest->title = $item[ "title" ];
                $quest->answer = isset( $item[ "answer" ] ) ? $item[ "answer" ]:"";
                $quest->description = isset( $item[ "description" ] ) ? $item[ "description" ]:"";
                $quest->hidden = isset( $item[ "hidden" ]) ? $item[ "hidden" ]:0;

                $result[$index]['data'] = $quest;
                $result[$index]['qu_id'] = $quest->id;

                $quest->save( );
                try {
                    $result[$index]['categories'] =
                        Category::addRelation( $item[ 'categories' ], $quest->id, 'question-subject' );
                }catch (QueryException $exception ) {
                    $result[$index]['categories']['error'] = $exception->getMessage();
                    $result[$index]['categories']['error_code'] = $exception->getCode();
                }
            }

            return response($result);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $id = null)
    {
        $editing =  $request->getMethod() == "PUT";

        if( !$editing && request('questions' ) && is_array(request('questions') ) ) {
            return $this->saveBundle( $request );
        }

        $question = $editing ? Question::find( $id ):new Question( );

        if( !$question )
            return $this->setAndGetResponse( 'message' , 'Quiz not found!');

        $uniqueQuestion = Rule::unique('questions' )->where(function ($query) {
            return $query->where( [ 'answer' => request('answer' ) ]);
        });

        if( $editing ) {
            $uniqueQuestion->ignore( $id );
        }

        $data = $request->validate([
            'title' => ['required', $uniqueQuestion],
            'hidden' => 'numeric',
            'description' => 'max:800',
            'categories' => 'required',
            'option_sets' => 'array'
        ]);

        $question->title = $data[ 'title' ];
        $question->answer = request( 'answer' );
        $question->description = $data[ 'description' ];
        $question->user_id = $request->user()->id;
        $question->hidden = request( 'hidden' )?:0;

        $data_opt_sets = $data['option_sets'];


        //dd($question);

        $question->save( );

        $this->set( 'meta_message', '' );

        foreach( $data_opt_sets as $set ){
            $name = isset($set['meta_name']) ? $set['meta_name']:'';
            $option_string = isset($set['option_string']) ? $set['option_string']:'';

            try{
                \GuzzleHttp\json_decode($option_string);
                $question->save_meta( $name, $option_string, 'opts',
                    isset($set['meta_id']) ? $set['meta_id']:0 );
            }catch ( GuzzleException $e ){
                $this->set( 'meta_message', 'Invalid question option set' );
            }
        }


        $catResult = Category::addRelation( request('categories'), $question->id, 'question-subject' );

        $this->set( 'message', $editing ? 'Question updated' : 'Question added' );
        $this->set( 'action', $editing ? 'updated' : 'added' );
        $this->set( 'success', true );
        $this->set( 'data', new QuestionResource($question) );
        $this->set( 'categories', $catResult );

        return  $this->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, $id = null)
    {
        $pDelete = $request->get( '_permanent' ) == "true";
        $bundleDelete = $request->get( '_bundle' ) == "true";
        $Ids = $request->get( '_bundle_ids' );

        $Ids = explode( ',',  urldecode( $Ids ) );

        if( !$bundleDelete ) {
            $Ids = [$id];
        }

        $question = $pDelete ? Question::whereIn( 'id', $Ids)->onlyTrashed( ) : Question::whereIn('id',$Ids)->withoutTrashed( );

//        dd( $question->get() );

        if ( $question->exists() ) {
            $deleted = 0;
            $this->set( 'data', $question->get() );

            if( $pDelete ) {
                if( QuestAssign::where( [ 'question_id' => $id ] )->exists() ) {
                    return $this->setAndGetResponse( 'message', 'Quiz assigned with question!', 403 );
                }

                if( $deleted = $question->forceDelete() ) {
                    $this->set('action', 'permanently-deleted');
                }
            }else {
                if( $deleted = $question->delete()) {
                    $this->set('action', 'deleted');
                }
            }

            if( $deleted > 0){
                $this->set( 'delete_count', $deleted );
                return $this->setAndGetResponse('message', "Question " . ( $pDelete ? " permanently ":"" ) . "deleted" );
            }
        }

        return  $this->setAndGetResponse( 'message' , 'Question not found!', 404 );
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore( Request $request, $id = null)
    {
        $questions = Question::whereIn( 'id', $this->ids( $id ) )->onlyTrashed( );

        if ( $questions->exists() ) {
            $this->set( 'data', $questions->get() );

            $restored = $questions->restore();

            if( $restored > 0 ){
                $this->set( 'count', $restored );
                $this->set( 'action', 'restored' );
                return $this->setAndGetResponse('message', "Question restored" );
            }
        }

        return  $this->setAndGetResponse( 'message' , 'Question not found!', 404 );
    }

}
