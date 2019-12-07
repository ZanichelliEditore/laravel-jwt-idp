<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 29/08/19
 * Time: 16.38
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class VerificationToken extends Model
{
    protected $table = 'verification_tokens';

    protected $fillable = [
        'token', 'user_id', 'is_valid'
    ];

}