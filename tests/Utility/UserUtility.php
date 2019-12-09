<?php

namespace Tests\Utility;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

class UserUtility
{

    /**
     * Login user
     * return Array cookies with token
     */
    public static function getAdmin()
    {
        $role = Role::firstOrCreate(['name' => 'ADMIN_IDP']);
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);

        factory(UserRole::class)->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        return $user;
    }
}
