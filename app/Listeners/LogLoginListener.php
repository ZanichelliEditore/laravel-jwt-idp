<?php

namespace App\Listeners;


use App\Events\LoginEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogLoginListener implements ShouldQueue {

    public function __construct(){

    }

    public function handle(LoginEvent $event){

        $user = $event->getUser();
        $ip = $event->getIp();

        $message = json_encode([
            'ip'     => $ip,
            'userId' => $user->id,
            'action' => 'Login',
            'url'    => route('login')
        ]);

        Log::info($message);
    }

    public function failed(LoginEvent $event, $exception){
        // Quando si verifica un problema con la coda e non viene processato
    }

}