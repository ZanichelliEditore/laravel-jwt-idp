<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 15.51
 */

namespace App\Services\Interfaces;


use App\Exceptions\SqlException;

interface IAccountService {

    /**
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $surname
     *
     * @throws SqlException
     */
    public function registerUser(string $email, string $password, string $name, string $surname);

    /**
     * @param string $verificationCode
     * @throws SqlException
     */
    public function verifyUser(string $verificationCode);

}