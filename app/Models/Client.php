<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'oauth_client_id',
        'oauth_client_secret',
        'scopes',
        'roles'
    ];

    public function oauthClient()
    {
        return $this->hasOne('App\Models\OauthClient', 'id');
    }
}
