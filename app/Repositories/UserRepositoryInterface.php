<?php

namespace App\Repositories;


use App\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the user paginated list.
     *
     * @param string|null $query
     * @return array
     */
    public function all($query = null);


    /**
     * Returns true if username is available, otherwise false.
     *
     * @param string $username
     * @return boolean
     */
    public function availableUsername(string $username);
}
