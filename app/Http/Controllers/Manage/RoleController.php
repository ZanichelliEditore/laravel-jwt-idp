<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 15/10/18
 * Time: 12.58
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller {

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
    public function all(){
        return Role::all();
    }

}