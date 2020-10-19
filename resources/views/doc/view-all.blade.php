<?php use \App\Doc\DocumentRequest;  ?>
@extends('doc.main')

@section('content')

    <div class="w-25 h-full fixed overflow-y-auto">
        <ul class="p-6">
            @if( isset($list_index) &&  is_array($list_index))
                @foreach( $list_index as $list_item )
                    <li> <a href="{{url( "#" . DocumentRequest::indexKey( $list_item['title']) )}}">{{ $list_item['title'] }}</a>

                        @if( isset( $list_item[ 'parents'] ) && is_array( $list_item[ 'parents'] ))
                            @foreach( $list_item[ 'parents'] as $parent_list )
                                <ul style="margin-left: 30px">
                                    <li>
                                        <a href="{{url( "#" . DocumentRequest::indexKey( $parent_list['title']) )}}">{{$parent_list['title']}}</a>
                                    </li>
                                </ul>
                            @endforeach
                        @endif
                    </li>
                @endforeach
            @endif
        </ul>
    </div>


    <div class="w-75 ml-auto">
        <div class="p-6">
            @if( isset($list_index) &&  is_array($list_index))
                @foreach( $list_index as $list_item )
                    <div class="flex-col">
                        @include("doc/request-item", ['item'=> $list_item] )

                        @if( isset( $list_item[ 'parents'] ) && is_array( $list_item[ 'parents'] ))
                            @foreach( $list_item[ 'parents'] as $parent_list )
                                @include("doc/request-item", ['item'=> $parent_list] )
                            @endforeach
                        @endif

                    </div>
                @endforeach
            @endif
        </div>
        <div class="footer" style="min-height: 500px">

        </div>
    </div>

{{--    <div class="table-responsive">--}}
{{--        <table class="table table-striped">--}}
{{--            <thead>--}}
{{--                <tr>--}}
{{--                    <th>name</th>--}}
{{--                    <th>description</th>--}}
{{--                    <th>type</th>--}}
{{--                </tr>--}}
{{--            </thead>--}}

{{--        </table>--}}
{{--    </div>--}}

@endsection
