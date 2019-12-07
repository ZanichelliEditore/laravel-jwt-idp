<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OauthClientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        return [
            "oauth_client_id" => $this->id,
            "oauth_name" => $this->name,
            "roles" =>  $this->client ? json_decode($this->client->roles) : json_decode("[]")
        ];
    }
}
