<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 11/10/18
 * Time: 15.43
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {


    /**
     * @OA\Put(
     *     path="/v1/user",
     *     summary="create a new user employee",
     *     description="Create a new user. Online for employee",
     *     operationId="create",
     *     tags={"User management"},
     *     security={{"passport":{"manage-user"}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="User e-mail. It is not mandatory",
     *                     type="string",
     *                     example="mario.rossi@email.com"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="User name",
     *                     type="string",
     *                     example="mario"
     *                 ),
     *                 @OA\Property(
     *                     property="surname",
     *                     description="User surname",
     *                     type="string",
     *                     example="rossi"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     description="Username",
     *                     type="string",
     *                     example="mario.rossi"
     *                 )
     *             )
     *          )
     *     ),
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
    public function create(Request $request){
        $credentials = $request->only('email', 'name', 'surname', 'username');

        $validator = $this->validator($credentials);
        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $user = $this->createUser($credentials);

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/v1/user/available/{username}",
     *     summary="Check if a username exists in the user table",
     *     description="Check if a username exists in the user table",
     *     operationId="availableUsername",
     *     tags={"User management"},
     *     security={{"passport":{"manage-user"}}},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         description="Username",
     *         required=true,
     *        @OA\Schema(
     *            type="string"
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
     *         response=400,
     *         description="Missing parameter 'username'",
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
    public function availableUsername(Request $request, $username){

        $available = User::where('username', $username)->doesntExist();

        return response()->json([
            'available' => $available
        ]);
    }

    /**
     * @OA\Get(
     *     path="/v1/employees",
     *     summary="returns all employees",
     *     description="Returns the list of employees",
     *     operationId="employees",
     *     tags={"User management"},
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
    public function employees(Request $request){

        $employees = User::where('is_employee', true)->get();

        return response()->json([
           'employees' => $employees
        ]);
    }

    /*
     * Returns the validator for user data
     */
    private function validator(array $data){
        return Validator::make($data, [
            'username' => 'required|string|max:50|unique:users',
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:50',
            'email' => 'string|email|max:255',
        ]);
    }

    /**
     * Returns a new user instance
     *
     * @param array $credentials
     * @return User
     */
    private function createUser(array $credentials){
        return User::create([
            'username' => $credentials['username'],
            'email' => $credentials['email'],
            'name' => $credentials['name'],
            'surname' => $credentials['surname'],
            'is_verified' => true
        ]);
    }

}