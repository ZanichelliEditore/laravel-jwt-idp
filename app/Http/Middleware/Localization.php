<?php
/**
 * Created by IntelliJ IDEA.
 * User: abs02
 * Date: 13/04/2018
 * Time: 15:28
 */

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization {

    public function handle($request, Closure $next){
        if(Session::has('locale')){
            App::setLocale(Session::get('locale'));
        }
        return $next($request);
    }

}