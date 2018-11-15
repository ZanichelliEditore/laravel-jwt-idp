<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 10.50
 *
 * @author Andrea De Castri
 */

namespace App\Listeners;


use App\Events\RegistrationEvent;
use App\Mail\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Class RegistrationListener
 * @package App\Listeners
 */
class RegistrationListener implements ShouldQueue {

    public function __construct(){

    }

    public function handle(RegistrationEvent $event){

        Log::info("Invio evento");

        $user = $event->getUser();
        $verificationCode = $event->getVerificationCode();

        Mail::to($user)->send(new UserRegistered($user, $verificationCode));
    }

    public function failed(RegistrationEvent $event, $exception){
        // error queue
    }

}