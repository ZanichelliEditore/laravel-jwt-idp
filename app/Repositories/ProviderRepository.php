<?php
namespace App\Repositories;

use App\Models\Provider;
use Illuminate\Support\Facades\Config;

class ProviderRepository extends BaseRepository
{


    /**
     * Creates and returns a provider..
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $logoutUrl = $data['logoutUrl'] ?: 'https://' . $data['domain'] . Config::get('app.default_provider_logout');

        return Provider::create([
            'domain' => $data['domain'],
            'username' => encrypt($data['username']),
            'password' => encrypt($data['password']),
            'logoutUrl' => $logoutUrl
        ]);
    }

    /**
     * Finds a @see Provider by id.
     *
     * @param integer $id
     * @return Provider
     */
    public function find(int $id)
    {
        return Provider::find($id);
    }

    /**
     * Returns a list of all @see Provider
     *
     * @return array
     */
    public function all()
    {
        return Provider::all();
    }

    /**
     * Updates a provider by id.
     *
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(int $id, array $data)
    {
        return Provider::where('id', $id)->update($data);
    }
}
