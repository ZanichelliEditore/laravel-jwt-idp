<?php

use Illuminate\Support\Str;

$factory->define(App\Models\Provider::class, function () {
    return [
        'domain' => Str::random(20),
        'username' => encrypt(Str::random(20)),
        'password' => encrypt(Str::random(20)),
        'logoutUrl' => Str::random(20),
    ];
});