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
            'username' => $data['username'],
            'email' => isset($data['email']) ? $data['email'] : null,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
            'is_verified' => isset($data['employee']) && $data['employee'],
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

        return User::where('username', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
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

    /**
     * Returns true if username is available, otherwise false.
     *
     * @param string $username
     * @return boolean
     */
    public function availableUsername(string $username)
    {
        return User::where('username', $username)->doesntExist();
    }
}
