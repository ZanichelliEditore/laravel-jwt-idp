<?php
namespace App\Repositories;

use App\Models\VerificationToken;

class VerificationTokenRepository extends BaseRepository
{
    /**
     * Creates and returns an instance of token.
     *
     * @param array $data
     * @return VerificationToken
     */
    public function create(array $data)
    {
        return VerificationToken::create($data);
    }

    /**
     * Finds a Verification Token by $id.
     *
     * @param integer $id
     * @return VerificationToken
     */
    public function find(int $id)
    {
        return VerificationToken::find($id);
    }

    /**
     * Returns a list of all @see VerificationToken.
     *
     * @return array
     */
    public function all()
    {
        return VerificationToken::all();
    }

    /**
     * Updates a Verification Token by id.
     *
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(int $id, array $data)
    {
        return VerificationToken::where('id', $id)->update($data);
    }

    /**
     * Find Verification token by token param
     * 
     * @param string $token
     * @return VerificationToken 
     */
    public function retrieveByToken(string $token) 
    {
        return VerificationToken::where('token', $token)->first();
    }
}
