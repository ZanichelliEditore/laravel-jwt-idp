<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 22/02/19
 * Time: 15.56
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Attribute
 * @package App\Models
 *
 * @property string $domain
 * @property string $username
 * @property string $password
 * @property string $logoutUrl
 */
class Provider extends Model {

    protected $table = 'providers';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain', 'logoutUrl', 'username', 'password'
    ];

}