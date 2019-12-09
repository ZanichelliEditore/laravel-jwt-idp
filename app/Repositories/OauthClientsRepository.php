<?php

namespace App\Repositories;

use App\Models\OauthClient;
use App\Models\Client;
use App\Http\Resources\OauthClientsResource;
use Illuminate\Database\Eloquent\Builder;

class OauthClientsRepository extends BaseRepository
{
    /**
    * @purpose
    *
    * Return a resource of all Oauth Clients, filtered by name and roles
    * @param  $query
    * @return OauthClientsResource
    *
    */

    public function all($query = null)
    {
        if (empty($query)) {
            return OauthClientsResource::collection(OauthClient::paginate(10));
        }

        $words = explode(' ', $query);
        $words = array_filter($words);

        $params = [];
        foreach($words as $key => $word) {
            if ($word) {
                $searchWord = '%' . $word . '%';
                $words[$key] =  "(name LIKE ? or roles like ? )" ;
                array_push($params, $searchWord, $searchWord);
            }
        }
        $searchTerm = implode(' and ', $words);

        $result = OauthClient::join('clients', 'oauth_clients.id', '=', 'clients.oauth_client_id' )
                                ->select('oauth_clients.*')
                                ->whereRaw($searchTerm, $params)
                                ->paginate(10);

        return OauthClientsResource::collection($result);
    }

    /**
    * @purpose
    *
    * Create new Oauth Clients
    * @param array $data
    * @return OauthClient
    */

    public function create(array $data)
    {
        return OauthClient::create($data);
    }

    /**
    * @purpose
    *
    * Find an Oauth Client by id
    * @param int $id
    * @return OauthClient
    */

    public function find(int $id)
    {
        return OauthClient::find($id);
    }

    /**
     * Update OauthClient 
     * @param int &id array $data
     * @param array $data
     * @return OauthClient
     */

    public function update(int $id, array $data)
    {
        return OauthClient::where('id', $id)->update($data);
    }
}