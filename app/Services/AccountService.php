<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 15.51
 */

namespace App\Services;


use App\Events\RegistrationEvent;
use App\Exceptions\SqlException;
use App\Models\Account\User;
use App\Models\Account\VerificationCode;
use App\Services\Interfaces\IAccountService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountService implements IAccountService {

    /**
     * AccountService constructor.
     */
    public function __construct(){}

    /**
     * Registers new user into the idp. After registration the user will be
     * notified by e-mail.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $surname
     *
     * @throws SqlException
     */
    public function registerUser(string $username, string $email, string $password, string $name, string $surname){

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $email,
                'email' => $email,
                'password' => Hash::make($password),
                'name' => $name,
                'surname' => $surname
            ]);

            $verificationCode = VerificationCode::create([
                'user_id' => $user->id,
                'verification_code' => str_random(30)
            ]);
        } catch (Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();

            throw new SqlException($e->getMessage());
        }

        DB::commit();

        event(new RegistrationEvent($user, $verificationCode));
    }

}