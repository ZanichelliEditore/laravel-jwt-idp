<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {

    protected $table = 'user_roles';

    protected $hidden = [
        'id', 'department_id', 'role_id', 'user_id'
    ];

    /**
     * @return mixed
     */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * @return mixed
     */
    public function role(){
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

}
