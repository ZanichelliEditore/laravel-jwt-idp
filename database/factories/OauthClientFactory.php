<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\OauthClient;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(OauthClient::class, function (Faker $faker) {
    return [
        'user_id' => null,
        'name' => $faker->name,
        'secret' => Str::random(20),
        'redirect' => 'http://localhost:8081/auth/callback',
        'personal_access_client' => 0,
        'password_client' => 0,
        'revoked' => 0
    ];
});
