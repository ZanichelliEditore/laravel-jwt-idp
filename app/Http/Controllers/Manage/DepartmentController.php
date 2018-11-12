<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 15/10/18
 * Time: 12.58
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller {

    /**
     * @OA\Get(
     *     path="/v1/departments",
     *     summary="list of departments",
     *     description="Returns the entire list of departments",
     *     operationId="all",
     *     tags={"Departments"},
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
    public function all(Request $request){
        return Department::all();
    }

}