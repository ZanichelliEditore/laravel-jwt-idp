<?php


namespace App\Http\Controllers\JwtAuth;


use App\Http\Controllers\Controller;
use App\Repositories\RepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    private $verificationTokenRepository;
    private $userRepository;


    public function __construct(RepositoryInterface $verificationTokenRepository, UserRepositoryInterface $userRepository)
    {
        $this->verificationTokenRepository = $verificationTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     path="/v1/complete-registration",
     *     summary="Active user",
     *     description="Activate user using token received in the email",
     *     operationId="VerificationController.verify",
     *     tags={"JWT Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="token",
     *                     description="Token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string",
     *                     format="password"
     *                 )
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="General error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function verify(Request $request)
    {
        $validator = $this->validator($request->only('token', 'password'));
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid parameters.'
            ], 422);
        }

        $token = $request->input('token');
        $password = $request->input('password');

        $hashedPassword = Hash::make($password);

        $verificationToken = $this->verificationTokenRepository->retrieveByToken($token);

        if (empty($verificationToken) || !$verificationToken->is_valid) {
            return response()->json([
                'message' => 'Invalid token'
            ], 422);
        }

        $user = $this->userRepository->find($verificationToken->user_id);

        DB::beginTransaction();

        $user->is_verified = true;
        $user->password = $hashedPassword;

        $verificationToken->is_valid = false;

        if (!$user->save() || !$verificationToken->save()) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error during user activation'
            ], 500);
        }

        DB::commit();

        return response()->json([], 204);
    }

    private function validator($parameters)
    {
        return Validator::make($parameters, [
            'token' => 'required|string',
            'password' => 'required|min:5'
        ]);
    }
}
