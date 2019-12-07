<?php

namespace App\Http\Controllers\Manage;


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Http\Requests\UserRoleRequest;
use App\Http\Resources\RoleResource;
use App\Repositories\UserRoleRepository;
use App\Repositories\UserRepositoryInterface;


class UserRoleController extends Controller
{
    protected $userRoleRepository;
    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRoleRepository $userRoleRepository, UserRepositoryInterface $userRepository,  RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @OA\Post(
     *     path="/v1/users/{id}/user-roles",
     *     summary="Assigne role to user",
     *     description="__*Security:*__ __*can be used only by clients with 'manager' role*__",
     *     operationId="UserRole.create",
     *     tags={"User-Role"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *        in="path",
     *        required=true,
     *        description="User id",
     *        name="id",
     *        @OA\Schema(
     *            type="integer",
     *            minimum=1
     *        )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(
     *                      @OA\Property(
     *                          property="role_id",
     *                          description="Id of role",
     *                          type="integer",
     *                          example=1
     *                      ),
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
     *         response=204,
     *         description="Operation successfull, user already has role passed",
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
     *         response=401,
     *         description="Unauthorized",
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
    public function create(UserRoleRequest $request, int $id)
    {
        $roleList = $request->all();
        $userRoles = [];
        if (!$this->userRepository->find($id)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['user_id' => 'The selected user_id not exists']
            ], 422);
        }
        $data['user_id'] = $id;
        DB::beginTransaction();
        foreach ($roleList as $userRole) {

            $data['role_id'] = $userRole['role_id'];

            if (count($this->userRoleRepository->where($data)) > 0) {
                continue;
            }

            $created = $this->userRoleRepository->create($data);

            if (!$created) {
                DB::rollBack();
                return response()->json(["message" => "Error on save, retry"], 503);
            }

            $userRoles[] = $created;
        }
        DB::commit();
        $status = ($userRoles) ? 201 : 204;

        return response()->json(
            [
                'data' => $userRoles
            ],
            $status
        );
    }

    /**
     * @OA\Delete(
     *     path="/v1/user-role/{id}",
     *     summary="Remove role to user by user-role-id",
     *     description="__*Security:*__ __*can be used only by clients with 'manager' role*__",
     *     operationId="UserRole.delete",
     *     tags={"User-Role"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *        in="path",
     *        required=true,
     *        description="User-role id",
     *        name="id",
     *        @OA\Schema(
     *            type="integer",
     *            minimum=1
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
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
     *         description="Error on saving",
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
    public function delete($id)
    {
        $userRole = $this->userRoleRepository->find($id);
        if (!$userRole) {
            return response()->json([
                'message' => 'User_role id not found'
            ], 404);
        }
        if (!$this->userRoleRepository->delete($userRole)) {
            return response()->json([
                'message' => 'Error on deleting'
            ], 500);
        }
        return response('', 204);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{id}/user-roles",
     *     summary="Retrieve role of user",
     *     description="Retrieve role of user",
     *     operationId="UserRole.retrieve-user",
     *     tags={"User-Role"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *        in="path",
     *        required=true,
     *        description="User id",
     *        name="id",
     *        @OA\Schema(
     *            type="integer",
     *            minimum=1
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
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
    public function getUserRole($id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $userRoles = $this->userRoleRepository->where(['user_id' => $id]);

        return RoleResource::collection($userRoles);
    }

    /**
     * Get every relationship user-roles given a role-id
     *
     * @param int $id
     * @return Response
     */
    public function getRoles($id)
    {
        $role = $this->roleRepository->find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }
        $userRoles = $this->userRoleRepository->where(['role_id' => $id]);

        return response()->json([
            'data' => $userRoles
        ], 200);
    }
}
