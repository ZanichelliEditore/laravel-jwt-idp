<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Creates and returns a @see User.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'surname' => isset($data['surname']) ? $data['surname'] : null,
            'password' => bcrypt($data['password']),
            'is_verified' => false,
        ]);
    }

    /**
     * Finds a @see User by id.
     *
     * @param integer $id
     * @return User
     */
    public function find(int $id)
    {
        return User::find($id);
    }

    /**
     * Returns a paginated list of all @see User.
     *
     * @param null $query
     * @return array
     */
    public function all($query = null)
    {
        if (empty($query)) {
            return User::paginate(10);
        }

        return User::where('email', 'like', '%' . $query . '%')
            ->paginate(10);
    }

    /**
     * Updates a @see User.
     *
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(int $id, array $data)
    {
        return User::where('id', $id)->update($data);
    }


}
