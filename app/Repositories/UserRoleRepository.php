<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserRole;

class UserRoleRepository extends BaseRepository
{
     /**
     * Creates and returns an instance of user-role.
     *
     * @param array $data
     * @return UserRole
     */
    public function create(array $data)
    {
        return UserRole::create($data);
    }

    /**
     * Finds a relation by $id.
     *
     * @param integer $id
     * @return UserRole
     */
    public function find(int $id)
    {
        return UserRole::find($id);
    }

    /**
     * Finds a roles by condition in $data.
     *
     * @param array $data
     * @return UserRole
     */
    public function where(array $data)
    {
        return UserRole::where($data)->get();
    }

    /**
     * Returns a list of all @see user-roles.
     *
     * @return array
     */
    public function all()
    {
        return UserRole::all();
    }

    /**
     * Updates a user-role by id.
     *
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(int $id, array $data)
    {
        return UserRole::find($id)->update($data);
    }
}
