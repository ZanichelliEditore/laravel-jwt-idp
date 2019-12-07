<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class LogoutEvent {
    
    use SerializesModels;
    
    private $user;
    
    public function __construct(User $user){
        $this->user = $user;
    }
    
    /**
     * Returns the logged-out user
     * @return User
     */
    public function getUser(){
        return $this->user;
    }
    
}

