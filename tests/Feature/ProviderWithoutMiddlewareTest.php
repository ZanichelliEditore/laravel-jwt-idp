<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Repositories\ProviderRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Utility\UserUtility;


class ProviderWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Fail created user.
     * @test
     * @return void
     */
    public function createProviderFailTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $body = [
            'domain' => Str::random(85),
            'username' => 'user',
            'password' => 'secret',
            'logoutUrl' => 'valid'
        ];

        $mock = Mockery::mock(ProviderRepository::class)->makePartial()
                ->shouldReceive(['create'=> null])
                ->once()
                ->getMock();
        $this->app->instance('App\Repositories\ProviderRepository', $mock);

        $response = $this->json('POST', '/admin/providers', $body, $cookie);
        $response->assertStatus(500);
    }
}