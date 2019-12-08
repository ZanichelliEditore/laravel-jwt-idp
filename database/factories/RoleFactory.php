<?php

use Illuminate\Support\Str;

$factory->define(App\Models\Role::class, function () {
    return [
        'name' => Str::random(20),
    ];
});
