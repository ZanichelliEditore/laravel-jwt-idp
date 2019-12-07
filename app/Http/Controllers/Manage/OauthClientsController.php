<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Repositories\ClientRepository;
use App\Repositories\ClientRoleRepository;
use App\Repositories\OauthClientsRepository;

class OauthClientsController extends Controller
{
    /**
    * @purpose
    *
    * Provides methods that interact with
    * oauth clients table.
    *
    */
    protected $oauthClientsRepository;
    private $clientRepository;

    public function __construct(OauthClientsRepository $oauthClientsRepository ,ClientRoleRepository $clientRoleRepository, ClientRepository $clientRepository)
    {
        $this->oauthClientsRepository = $oauthClientsRepository;
        $this->clientRoleRepository = $clientRoleRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
    * @purpose
    *
    * Return a resource of all oauth clients
    * @param  Request  $request
    * @return void
    */

    public function all(Request $request)
    {   
        $query = $request->input('q');

        return $this->oauthClientsRepository->all($query);
    }

    /**
    * @purpose
    *
    * Update the roles of single oauth client
    * @param  Request  $request
    * @return void
    */

    public function updateClientRoles(Request $request) {
        
        $admittedRoles = $this->clientRoleRepository->all();

        $validatedData = $request->validate([
            'clientId' => 'required|integer',
            'roles' => ['array',
                Rule::in($admittedRoles)
            ]
        ]);

        $id = $validatedData['clientId'];
        $roles = json_encode($validatedData['roles']);

        $data = ["oauth_client_id" => $id,
                 "roles" => $roles   
        ];

        $oauthClient = $this->oauthClientsRepository->find($id);

        if (empty($oauthClient)) {
            return response()->json([
                'message' => 'Error during updating roles'
            ], 500);
        }
        
        if(empty($oauthClient->client)){
            if (!$this->clientRepository->create($data)) {
                return response()->json([
                    'message' => 'Error during updating roles'
                ], 500);
            }
        }

        $oauthClient->client = $this->clientRepository->update($id, $data);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
