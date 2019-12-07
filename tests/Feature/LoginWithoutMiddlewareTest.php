<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use App\Repositories\ProviderRepository;
use App\Http\Controllers\JwtAuth\LoginController;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Requests\LoginRequest;

class LoginWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @test
     * @return void
     */
    public function loginUserTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);

        $loginController = new LoginController($mock);

        $request = new LoginRequest([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);

        $request->merge([
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $response = $loginController->login($request);
        $this->assertEquals(200, $response->status());
        $this->assertContains('user', $response->getContent());
    }

    /**
     * @test
     * @return void
     */
    public function loginNotVerifiedUserTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => false
        ]);

        $loginController = new LoginController($mock);

        $request = new LoginRequest([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);

        $request->merge([
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $response = $loginController->login($request);
        $this->assertEquals(403, $response->status());
    }

    /**
     * Logout without provider register on system
     * @test
     * @return void
     */
    public function logoutWithoutProviderTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);
        $cookies = ['token' => json_decode($response->getContent())->token];

        $mock = Mockery::mock(ProviderRepository::class)->makePartial()
            ->shouldReceive(['all' => []])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\ProviderRepository', $mock);

        $response = $this->get('/v1/logout', [], $cookies);
        $response->assertStatus(302)->assertRedirect('/loginForm');
    }
}
