<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;

class UserWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Fail available user.
     * @test
     * @return void
     */
    public function notAvailableUsernameTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('GET', '/v1/user/available/' . $user->username);
        $response->assertStatus(200)->assertJson(['available' => false]);
    }

    /**
     * Success available username.
     * @test
     * @return void
     */
    public function availableUsernameTest()
    {
        $response = $this->json('GET', '/v1/user/available/' . Str::random(51));
        $response->assertStatus(200)->assertJson([
            'available' => true
        ]);
    }

    /**
     * Success find user.
     * @test
     * @return void
     */
    public function findUserTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('GET', '/v1/users/' . $user->id);
        $response->assertStatus(200);
    }


    /**
     * Find not exists user
     * @test
     * @return void
     */
    public function notFoundUserTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        User::destroy($user->id);
        $response = $this->json('GET', '/v1/users/' . $user->id);
        $response->assertStatus(404);
    }
}
