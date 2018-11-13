<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 10.48
 */

namespace App\Events;


use App\Models\User;
use Illuminate\Queue\SerializesModels;

class RegistrationEvent {

    use SerializesModels;

    private $user;
    private $verificationCode;

    public function __construct(User $user, string $verificationCode){
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    public function getUser(){
        return $this->user;
    }

    public function getVerificationCode(){
        return $this->verificationCode;
    }

}