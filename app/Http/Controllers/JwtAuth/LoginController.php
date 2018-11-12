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
use App\Models\Publisher;
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
        if($userSession != null){
            $url = $request->get('redirect') . '?token=' . $userSession['token'];
            return redirect()->away($url);
        }
        return view('auth.login');
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
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     description="User name",
     *                     type="string",
     *                     example="demouser" 
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string",
     *                     example="demopwd" 
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
     *     )
     * )
     */
    public function login(Request $request){

        /**
         * Viene effettuata una redirect se non sono presenti username e password
         * e se la chiamata NON ha nel header "Accept application/json".
         */
        if(!$request->has(['username', 'password']) && !$request->wantsJson()){
            redirect()->route('loginForm');
        }

        $credentials = $request->only('username', 'password');

        /**
         * Effettuo la validazione dei campi. Se si verifica un errore
         * torno alla pagina di login re-impostando la query nell'url.
         */
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
        // Check if user is verified only fot not employee
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

        if($request->wantsJson()){
            return $this->createResponse(true, $response, true);
        } else {
            $url = $request->get('redirect') . '?token=' . $token;
            return redirect()->away($url);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/loginWithToken",
     *     summary="retrieve user info in json format", 
     *     description="Use to retrieve user info with roles and departments",
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

        JWTAuth::parseToken()->refresh();
        auth()->logout();

        SessionManager::flushByUserId($user->id);

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
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:3',
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