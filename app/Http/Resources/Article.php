<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => "THIS IS BODY: ". $this->body,
            'image' => [ 'url' => url("a.img") ],
            'api_token' => $request->get('api_token'),
        ];
    }
}
