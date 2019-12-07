<?php

namespace App\Repositories;

use App\Models\Client;

interface ClientRepositoryInterface
{

    /**
     * Finds a @see Client by id.
     *
     * @param integer $id
     * @return Client
     */
    public function find(int $id);

    /**
     * Create new Client
     * @param array $data
     * @return Client
     */
    public function create($data);

    /**
     * Update Client 
     * @param int &id
     * @param array $data
     * @return Client
     */

    public function update(int $id, array $data);
}
