<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\Mailer;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller
{
    protected $userRepository;
    protected $verificationTokenRepository;
    protected $mailerService;

    public function __construct(UserRepositoryInterface $userRepository, RepositoryInterface $verificationToken, Mailer $mailerService)
    {
        $this->userRepository = $userRepository;
        $this->verificationTokenRepository = $verificationToken;
        $this->mailerService = $mailerService;
    }

    public function all(Request $request)
    {
        $query = $request->input('q');

        return $this->userRepository->all($query);
    }

    /**
     * @OA\Post(
     *     path="/v1/user",
     *     summary="create a new user",
     *     description="__*Security:*__ __*can be used only by clients with 'manager' role*__",
     *     operationId="create",
     *     tags={"User management"},
     *     security={{"passport":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email"},
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
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
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        $credentials = $request->only('email', 'name', 'surname');

        $validator = $this->validator($credentials);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data are invalid',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = $this->userRepository->create($credentials);

            $verificationToken = $this->verificationTokenRepository->create([
                'token' => Str::random(60),
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error during saving user'
            ], 500);
        }

        DB::commit();

        $body = view('mail.complete-registration', ['token' => $verificationToken->token, 'user' => $user])->render();
        $this->mailerService->dispatchEmail($body, [$user->email], 'Completa la registrazione');

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{id}",
     *     summary="Returns user by id",
     *     description="Returns user details by id",
     *     operationId="find",
     *     tags={"User management"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User id",
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
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function find($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return response()->json([], 404);
        }

        return response()->json([
            'user' => UserResource::make($user)
        ], 200);
    }

    
    /*
     * Returns the validator for user data
     */
    private function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50',
            'surname' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        return $validator;
    }
}
