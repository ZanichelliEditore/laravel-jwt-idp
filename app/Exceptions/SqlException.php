<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 13/11/18
 * Time: 16.03
 */

namespace App\Exceptions;


use Exception;
use Throwable;

class SqlException extends Exception {

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }

}