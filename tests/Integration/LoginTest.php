<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;

class LoginTest extends TestCase
{
    /**
     * get user without token.
     * @test
     * @return void
     */
    public function userTokenAbsentTest()
    {
        $response = $this->get('/v1/user');

        $response->assertStatus(400)
            ->assertJson([
                'message' => __('auth.token-absent'),
            ]);
    }

    /**
     * Login user verified
     * @test
     * @return void
     */
    public function userWithTokenTest()
    {
        $cookies = $this->loginUser();
        $response = $this->json('GET', 'v1/user', [], $cookies);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $this->getUserStructure()
            );
    }

    /**
     * Login user not verified
     * @test
     * @return void
     */
    public function userNotVerifiedTest()
    {
        $user = factory(User::class)->create();
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.err-verification'),
            ])
            ->assertCookieMissing('token');
    }

    /**
     * Login user verified
     * @test
     * @return void
     */
    public function userVerifiedTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => $this->getUserStructure(),
                'token'
            ])
            ->assertCookie('token');
    }

    /**
     * Login user with wrong data
     * @test
     * @return void
     */
    public function loginWrongDataTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'wrong'
        ]);
        $response->assertStatus(404)
            ->assertJson([
                'message' => __('auth.err-login'),
            ])
            ->assertCookieMissing('token');

        $response = $this->json('POST', 'v2/login', [
            'username' => 'error',
            'password' => 'secret'
        ]);
        $response->assertStatus(404)
            ->assertJson([
                'message' => __('auth.err-login'),
            ])
            ->assertCookieMissing('token');
    }

    /**
     * Login user without data
     * @test
     * @return void
     */
    public function loginMissingDataTest()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
        ]);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ]
        ])->assertCookieMissing('token');
        $response = $this->json('POST', 'v2/login', [
            'password' => 'secret'
        ]);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'username',
            ]
        ])->assertCookieMissing('token');
        $response = $this->json('POST', 'v2/login', []);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'password',
                'username',
            ]
        ])->assertCookieMissing('token');
    }

    /**
     * Logout without token accept json
     * @test
     * @return void
     */
    public function logoutJsonTest()
    {
        $cookies = $this->loginUser();
        $response = $this->json('GET', '/v1/logout', [], $cookies);
        $response->assertStatus(200);
    }

    /**
     * Logout without token
     * @test
     * @return void
     */
    public function logoutTest()
    {
        $cookies = $this->loginUser();
        $response = $this->get('/v1/logout', [], $cookies);
        $response->assertStatus(302)->assertRedirect('/loginForm');
    }

    /**
     * Logout without token with provider register on system
     * @test
     * @return void
     */
    public function logoutWithProviderTest()
    {
        $cookies = $this->loginUser();
        $provider = factory(Provider::class)->create();
        $response = $this->get('/v1/logout', [], $cookies);
        $response->assertStatus(302)->assertRedirect('/loginForm');
    }

    /**
     * Logout without token
     * @test
     * @return void
     */
    public function logoutTokenAbsentTest()
    {
        $response = $this->get('/v1/logout');

        $response->assertStatus(400)
            ->assertJson([
                'message' => __('auth.token-absent'),
            ]);
    }

    /**
     * Redirect to login form
     * @test
     * @return void
     */
    public function homeTest()
    {
        $response = $this->json('GET', '/');
        $response->assertStatus(302)
            ->assertRedirect('/loginForm');
    }

    /**
     * Check view on loginForm
     * @test
     * @return void
     */
    public function loginFormTest()
    {
        $response = $this->json('GET', '/loginForm');
        $response->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    /**
     * Authenticated user
     * @test
     * @return void
     */
    public function authenticatedUser()
    {
        $cookies = $this->loginUser();
        $response = $this->json('GET', '/authenticated', [], $cookies);
        $response->assertStatus(200)
            ->assertViewIs('auth.logged');
    }

    /**
     * Unauthenticated user
     * @test
     * @return void
     */
    public function unauthenticatedUser()
    {
        $response = $this->json('GET', '/authenticated');
        $response->assertStatus(302)->assertRedirect('/loginForm');
    }

    /**
     * Login user
     * return Array cookies with token
     */
    public function loginUser()
    {
        $user = factory(User::class)->create([
            'is_verified' => true
        ]);
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);
        return ['token' => json_decode($response->getContent())->token];
    }

    /**
     * Data returned for user
     * return Array
     */
    public function getUserStructure()
    {
        return [
            'id',
            'username',
            'email',
            'is_verified',
            'name',
            'surname',
            'roles',
        ];
    }
}
