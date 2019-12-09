<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Repositories\RepositoryInterface;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/roles",
     *     summary="list of roles",
     *     description="Returns the entire list of roles",
     *     operationId="all",
     *     tags={"Roles"},
     *     security={{"passport":{}}},
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
    public function all()
    {
        return $this->roleRepository->all();
    }

    /**
     * @OA\Post(
     *     path="/v1/roles",
     *     summary="Create a new role",
     *     description="__*Security:*__ __*can be used only by clients with 'admin' role*__",
     *     operationId="RoleController.create",
     *     tags={"Roles"},
     *     security={{"passport":{"manage-idp"}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="Role name",
     *                     type="string",
     *                     example="ADMIN_IDP"
     *                 )
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
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
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid scope or client role, Forbidden",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:roles,name'
        ]);

        $data = $request->only('name');

        try {
            $role = $this->roleRepository->create($data);

            return response()->json($role, 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error on saving role'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/roles/{id}",
     *     summary="Remove role by id",
     *     description="__*Security:*__ __*can be used only by clients with 'admin' role*__",
     *     operationId="RoleController.delete",
     *     tags={"Roles"},
     *     security={{"passport":{"manage-idp"}}},
     *     @OA\Parameter(
     *        in="path",
     *        required=true,
     *        description="Role id",
     *        name="id",
     *        @OA\Schema(
     *            type="integer",
     *            minimum=1
     *        )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error on deleting",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid scope or client role, Forbidden",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function delete(int $id)
    {
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return response()->json([
                'message' => 'Role id not found'
            ], 404);
        }

        if (!$this->roleRepository->delete($role)) {
            return response()->json([
                'message' => 'Error on deleting'
            ], 500);
        }

        return response()->json([], 204);
    }

}