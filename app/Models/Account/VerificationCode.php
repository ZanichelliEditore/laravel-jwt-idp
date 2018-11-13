<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 15.32
 */

namespace App\Models\Account;


use Illuminate\Database\Eloquent\Model;

/**
 * Class VerificationCode
 * @package App\Models
 *
 * @property int user_id
 * @property string verification_code
 */
class VerificationCode extends Model {

    protected $table = 'user_verifications';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'verification_code'
    ];

}