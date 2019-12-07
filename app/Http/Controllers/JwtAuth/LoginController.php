<?php

namespace App\Http\Controllers\JwtAuth;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Cookie;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Cookie as FacadeCookie;

class LoginController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Shows the login form or redirect the user to the application if he is authenticated.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Shows page for authenticated user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authenticated()
    {
        return view('auth.logged');
    }

    /**
     * @OA\Post(
     *     path="/v2/login",
     *     summary="generate a JWT token", 
     *     description="Use to generate access JWT token for user auth",
     *     operationId="v2/login",
     *     tags={"JWT Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     description="Username",
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
     *         response=200,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Authentication error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = $this->retrieveToken($credentials)) {
                return $this->createResponse(404, __('auth.err-login'));
            }
        } catch (JWTException $e) {
            Log::error($e->getMessage());
            return $this->createResponse(500, __('auth.err-jwt'));
        }

        $user = auth()->user();

        if (!$user->is_verified) {
            return $this->createResponse(403, __('auth.err-verification'));
        }

        $userResource = UserResource::make($user);

        event(new LoginEvent($user, $request->ip()));

        return response()->json([
            'user' => $userResource,
            'token' => $token
        ])->withCookie(new Cookie('token', $token, 0, '/', env('TOKEN_COOKIE_DOMAIN')));
    }

    /**
     * @OA\Get(
     *     path="/v1/user",
     *     summary="retrieve user info in json format", 
     *     description="Use to retrieve user info with roles",
     *     operationId="userByToken",
     *     tags={"JWT Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="JWT token vaule",
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
     *     )
     * )
     */
    public function userByToken()
    {
        $userResource = UserResource::make(auth()->user());

        return response()->json($userResource);
    }

    /**
     * @OA\Get(
     *     path="/v1/logout",
     *     summary="Logout the user and delete his session",
     *     description="Logout the user and delete his session",
     *     operationId="logout",
     *     tags={"JWT Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="JWT token vaule",
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
     *     )
     * )
     */
    public function logout(Request $request)
    {
        event(new LogoutEvent(auth()->user()));
        auth()->logout(true);

        $cookie = FacadeCookie::forget('token', '/', env('TOKEN_COOKIE_DOMAIN'));

        if ($request->ajax() || $request->wantsJson()) {
            return $this->createResponse(200, null, $cookie);
        }

        if ($request->input("redirect")) {
            return redirect(route('loginForm', [
                'redirect' => $request->input("redirect")
            ]))->withCookie($cookie);
        }

        return redirect('loginForm')->withCookie($cookie);
    }

    private function retrieveToken(array $credentials)
    {
        return auth()->attempt($credentials);
    }

    private function createResponse(int $status = 200, string $message = null, $cookie = null)
    {
        if (empty($message)) {
            $response = response()->json([], $status);
        }

        $response = response()->json([
            'message' => $message
        ], $status);

        if ($cookie) {
            return $response->withCookie($cookie);
        }
        return $response;
    }
}
