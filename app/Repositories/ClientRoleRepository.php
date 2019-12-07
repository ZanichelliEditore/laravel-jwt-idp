<?php
namespace App\Repositories;

use App\Constants\Role;
class ClientRoleRepository
{
 

    /**
     * Returns a list of all Client Roles.
     *
     * @return array
     */
    public function all()
    {
        return [Role::ADMIN, Role::MANAGER];
    }

    
}
