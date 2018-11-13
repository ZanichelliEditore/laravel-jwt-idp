<?php

namespace App\Mail;

use App\Models\Account\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable {

    use Queueable, SerializesModels;

    private $user;
    private $verificationCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $verificationCode){
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->markdown('emails.users.registered')->with([
            'user' => $this->user,
            'verificationCode' => $this->verificationCode
        ]);
    }

}
