<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Provider;
use Illuminate\Support\Str;
use Tests\Utility\UserUtility;

class ProviderTest extends TestCase
{
    /**
     * Get all providers for unauthorized user.
     * @test
     * @return void
     */
    public function providersUnauthorizedTest()
    {
        $response = $this->json('GET', '/v1/providers');
        $response->assertStatus(401);
    }

    /**
     * Create provider for unauthorized user.
     * @test
     * @return void
     */
    public function providerCreateUnauthorizedTest()
    {
        $response = $this->json('POST', '/v1/providers');
        $response->assertStatus(401);
    }

    /**
     * Error validation in create provider
     * @test
     * @return void
     */
    public function providerCreateValidationErrorByAdminTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $response = $this->json('POST', '/admin/providers', [], $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'domain',
                'username',
                'password'
            ]
        ]);

        $body = [
            'domain' => 123,
            'username' => 123,
            'password' => 123,
            'logoutUrl' => 123
        ];
        $response = $this->json('POST', '/admin/providers', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'domain',
                'username',
                'password',
                'logoutUrl'
            ]
        ]);

        $body = [
            'domain' => Str::random(256),
            'username' => Str::random(51),
            'password' => Str::random(51),
            'logoutUrl' => Str::random(256)
        ];
        $response = $this->json('POST', '/admin/providers', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'domain',
                'username',
                'password',
                'logoutUrl'
            ]
        ]);

        $provider = factory(Provider::class)->create();
        $body = [
            'domain' => $provider->domain,
            'username' => 'valid',
            'password' => 'a',
            'logoutUrl' => 'valid'
        ];
        $response = $this->json('POST', '/admin/providers', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'domain',
                'password',
            ]
        ]);
    }

    /**
     * Create provider by admin.
     * @test
     * @return void
     */
    public function providerCreateByAdminTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $body = [
            'domain' => Str::random(85),
            'username' => 'user',
            'password' => 'secret',
            'logoutUrl' => 'valid'
        ];
        $response = $this->json('POST', '/admin/providers', $body, $cookie);
        $response->assertStatus(201)
                ->assertJsonStructure([
            'provider' => [
                'id',
                'logoutUrl',
                'domain',
                'username',
                'password'
            ]
        ]);
    }
}
