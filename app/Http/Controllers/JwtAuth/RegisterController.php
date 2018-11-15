<?php

namespace App\Http\Controllers\JwtAuth;

use App\Exceptions\SqlException;
use App\Http\Controllers\Controller;
use App\Models\Account\User;
use App\Services\Interfaces\IAccountService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Created by IntelliJ IDEA.
 * User: Andrea De Castri, Michela Tarozzi
 * Date: 13/04/2018
 * Time: 11:54
 */
class RegisterController extends Controller {

    private $accountService;

    public function __construct(IAccountService $accountService){
        $this->accountService = $accountService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegisterForm(){
        return view('auth.register');
    }

    /**
     * Registers a new user after validate data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){

        $credentials = $request->only('email', 'password', 'password_confirmation', 'name', 'surname');

        $validator = $this->validator($credentials);

        if($validator->fails()){

            if($request->ajax() || $request->wantsJson()){
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 400);
            } else {
                return redirect('registerForm')
                    ->withErrors($validator);
            }
        }

        try {

            $this->accountService->registerUser($credentials['email'],
                $credentials['password'], $credentials['name'], $credentials['surname']);
        } catch (SqlException $e) {

            if($request->ajax() || $request->wantsJson()){
                return response()->json([
                    'success' => false,
                    'message' => 'Fatal error'
                ], 500);
            } else {
                return redirect('registerForm')
                    ->withErrors([
                        'message' => 'Fatal error'
                    ]);
            }
        }

        if($request->ajax() || $request->wantsJson()){
            return response()->json([
                'success' => true,
                'message' => 'User registered'
            ]);
        } else {
            return redirect('loginForm')->with([
                'success' => __('auth.label-registration-success')
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $code){
        // TODO ci può accedere solo se guest
        try {
            $this->accountService->verifyUser($code);
        } catch (Exception $e){
            // TODO controllare se è ajax oppure non ajax
            return redirect('loginForm')->withErrors([
                'message' => [
                    __('auth.err-verification-code')
                ]
            ]);
        }

        // TODO controllare se è ajax oppure non ajax
        return redirect('loginForm')->with([
            'success' => __('auth.label-account-actived')
        ]);
    }

    /*
     * Returns the validator for user data
     */
    private function validator(array $data){
        return Validator::make($data, [
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Creates a validation code to send to user
     * @param User $user
     */
    private function createVerificationCode(User $user){

        $verificationCode = str_random(30);
        DB::table('user_verifications')->insert([
            'user_id' => $user->id,
            'verification_code' => $verificationCode
        ]);

        return $verificationCode;
    }

}