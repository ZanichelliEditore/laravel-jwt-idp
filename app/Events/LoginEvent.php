<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 14/09/18
 * Time: 14.51
 */

namespace App\Events;

use App\Models\Account\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class LoginEvent
 * @package App\Events
 *
 */
class LoginEvent {

    use SerializesModels;

    private $user;
    private $ip;

    public function __construct(User $user, string $ip){
        $this->user = $user;
        $this->ip = $ip;
    }

    /**
     * Returns the current logged user
     * @return User
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * Returns the request of the user
     * @return string
     */
    public function getIp(){
        return $this->ip;
    }

}