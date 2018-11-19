<?php
/**
 * Created by IntelliJ IDEA.
 * User: abs02
 * Date: 13/04/2018
 * Time: 12:44
 */

namespace App\Http\Controllers\JwtAuth;

use App\Events\LoginEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Notification\Publisher;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller {

    /**
     * Shows the login form or redirect the user to the application if he is authenticated.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm(Request $request){

        $userSession = $this->getUserSession($request);

        if(empty($userSession)){
            return view('auth.login');
        }

        $redirectUrl = $request->input('redirect');

        if(empty($redirectUrl)){
            return view('auth.logged');
        }

        if($userSession != null){
            $url = $redirectUrl . '?' . build_query(['token' => $userSession['token']]);
            return redirect()->away($url);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="generate a JWT token", 
     *     description="Use to generate access JWT token for user auth",
     *     operationId="login",
     *     tags={"JWT Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="User e-mail",
     *                     type="string",
     *                     example="mario.rossi@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string",
     *                     example="secret"
     *                 )
     *             )
     *          )
     *     ),     
     *     @OA\Response(
     *         response=200,
     *         description="Operation successful",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function login(Request $request){

        /**
         * Redirect if request haven't username or password and the request
         * haven't "Accept application/json" header.
         */
        if(!$request->has(['email', 'password']) && !$request->wantsJson()){
            redirect()->route('loginForm');
        }

        $credentials = $request->only('email', 'password');

        $validator = $this->validator($credentials);
        if($validator->fails()){
            return $this->createResponse(
                false,
                $validator->errors()->messages(),
                $request->wantsJson(),
                400,
                $request->input('redirect'));
        }

        try {
            if(!$token = JWTAuth::attempt($credentials)){
                $message = [
                    'message' => __('auth.err-login')
                ];
                return $this->createResponse(false, $message, $request->wantsJson(), 401, $request->input('redirect'));
            }
        } catch (JWTException $e){
            $message = [
                'message' => __('auth.err-jwt')
            ];
            return $this->createResponse(false, $message, $request->wantsJson(), 500);
        }

        $user = JWTAuth::user();

        if(!$user->is_verified){
            $message = [
                'message' => __('auth.err-verification')
            ];
            return $this->createResponse(false, $message, $request->wantsJson());
        }

        $userResource = UserResource::make($user);

        $this->createSession($request, $userResource, $token);

        $response = [
            'token' => $token,
            'user' => $userResource
        ];

        event(new LoginEvent($user, $request->ip()));

        if($request->ajax() || $request->wantsJson()){
            return $this->createResponse(true, $response, true);
        } else {

            $redirect = $request->input('redirect');

            if(empty($redirect)){
                return view('auth.logged');
            }

            $url = $redirect . '?token=' . $token;

            return redirect()->away($url);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/loginWithToken",
     *     summary="retrieve user info in json format", 
     *     description="Use to retrieve user info with roles",
     *     operationId="loginWithToken",
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
    public function loginWithToken(Request $request){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token expired'
            ], 403);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Invalid token'
            ], 403);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token absent'
            ], 403);
        }

        $userResource = UserResource::make($user);

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
    public function logout(Request $request){

        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token expired'
            ], 403);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Invalid token'
            ], 403);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token absent'
            ], 403);
        }

        // Decomment to send a message to queues with rabbitmq
//        $publisher = Publisher::create([
//            // Channels to notify
//        ], $user);
//
//        $publisher->send([
//            'user_id' => $user->id
//        ]);

        $request->session()->flush();

        SessionManager::flushByUserId($user->id);

        auth()->logout(true, true);

        return response([], 200);
    }

    private function getUserSession(Request $request){
        $token = $request->session()->get('token');

        if(!$token){
            return null;
        }

        JWTAuth::setToken($token);
        if(!JWTAuth::check()){
            return null;
        }

        $user = $request->session()->get('user');
        $userSession = array(
            'user' => $user,
            'token' => $token,
        );

        return $userSession;
    }

    private function createSession(Request $request, $user, $token){
        $userSession = array(
            'user' => $user,
            'token' => $token
        );
        $request->session()->put($userSession);
    }

    /*
     * Returns the validator for user data
     */
    private function validator(array $data){
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
    }

    private function createResponse($success, array $messages, $acceptJson, $status = 200, $queryRedirect = null){
        if($acceptJson){
            $messages['success'] = $success;
            return response()->json($messages, $status);
        } else {
            $queryString = '';
            if(!empty($queryRedirect)){
                $queryString = $queryString . '?redirect=' . $queryRedirect;
            }
            return redirect('loginForm' . $queryString)
                ->withErrors($messages)
                ->withInput();
        }
    }

}