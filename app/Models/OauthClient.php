<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    protected $table = 'oauth_clients';

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'oauth_client_id');
    }
}
