<?php use \App\Doc\DocumentRequest;  ?>
<div style="margin-left: 5px; min-height: {{ $item['is_group_title'] ? "5px":"300px" }}">
    <h2>
        <a name="{{DocumentRequest::indexKey( $item['title'])}}"
           href="{{url( "#" . DocumentRequest::indexKey( $item['title']) )}}">{{ $item['title'] }}</a>
    </h2>

    @if( !$item['is_group_title'] )
        @php $item = DocumentRequest::findItemById( $item['id'] ) @endphp

        <p>{{$item->method}} {{$item->url}}</p>
    @endif

</div>

