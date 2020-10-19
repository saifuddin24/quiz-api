<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $data = [
            "id"=> $this->id,
            "first_name"=> $this->first_name,
            "last_name"=> $this->last_name,
            "display_name"=> $this->display_name,
            "email"=> $this->email,
            "phone_number"=> $this->phone_number,
            "profile_pic"=> $this->profile_pic,
            "user_from"=> $this->user_form,
            "gender"=> $this->gender,
            "social_user_id"=> $this->social_user_id,
            "lang"=> $this->lang,
            "ip_address"=> $this->ip_address,
            "metadata"=> (object) $this->metadata(),
            "usertype"=> $this->usertype,
            "email_verified_at"=> $this->email_verified_at,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            "deleted_at"=> $this->deleted_at,
            "isAdmin"=> $this->isAdmin()
        ];

        if(  !$this->isAdmin() ) {
            unset($data['isAdmin']);
            unset($data['created_at']);
            unset($data['updated_at']);
            unset($data['deleted_at']);
            unset($data['usertype']);
            unset($data['email_verified_at']);
        }

        return $data;

    }
}
