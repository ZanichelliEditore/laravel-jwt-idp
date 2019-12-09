<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Finds a @see Client by id.
     *
     * @param integer $id
     * @return Client
     */
    public function find(int $id)
    {
        return Client::find($id);
    }

     /**
     * Create new Client
     * @param array $data
     * @return Client
     */
    public function create($data)
    {
        return Client::create($data);
    }

    /**
     * Update Client 
     * @param int &id array $data
     * @param string $data
     * @return Client
     */

    public function update(int $id, array $data)
    {
        return Client::where('id', $id)->update($data);
    }
}
