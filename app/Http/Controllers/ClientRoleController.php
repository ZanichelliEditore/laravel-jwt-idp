<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClientRoleRepository;

class ClientRoleController extends Controller
{
    private $clientRoleRepository;

    public function __construct(ClientRoleRepository $clientRoleRepository)
    {
        $this->clientRoleRepository = $clientRoleRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/client-roles",
     *     summary="list of  client roles",
     *     description="Returns the entire list of client roles",
     *     operationId="all",
     *     tags={"ClientRoles"},
     *     security={{"web":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function all(){

        return $this->clientRoleRepository->all();
    }
}
