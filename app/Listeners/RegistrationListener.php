<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 10.50
 */

namespace App\Listeners;


use App\Events\RegistrationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationListener implements ShouldQueue {

    public function __construct(){

    }

    public function handle(RegistrationEvent $event){

        $user = $event->getUser();
        $verificationCode = $event->getVerificationCode();

        // TODO send confirmation email
    }

    public function failed(RegistrationEvent $event, $exception){
        // error queue
    }

}