<?php
namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    /**
     * Creates and returns a @see Role.
     *
     * @param array $data
     * @return Role
     */
    public function create(array $data)
    {
        return Role::create($data);
    }

    /**
     * Finds a @see Role by $id.
     *
     * @param integer $id
     * @return Role
     */
    public function find(int $id)
    {
        return Role::find($id);
    }

    /**
     * Returns a list of all @see Role.
     *
     * @return array
     */
    public function all()
    {
        return Role::all();
    }

    /**
     * Updates a role by id.
     *
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(int $id, array $data)
    {
        return Role::where('id', $id)->update($data);
    }
}
