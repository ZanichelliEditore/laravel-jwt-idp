<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 09/11/18
 * Time: 10.10
 */

namespace App\Http\Controllers\JwtAuth;


use Illuminate\Support\Facades\DB;

class SessionManager {

    /**
     * Deletes the sessions of a specific user
     * @param int $userId
     */
    public static function flushByUserId(int $userId){
        DB::table('sessions')
            ->where('user_id', '=', $userId)
            ->delete();
    }

}