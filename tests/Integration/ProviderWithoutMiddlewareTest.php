<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Provider;

class ProviderWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Get all providers by unauthorized user.
     * @test
     * @return void
     */
    public function providersTest()
    {
        factory(Provider::class)->create();
        $response = $this->json('GET', '/v1/providers');
        $response->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'domain',
                    'username',
                    'password',
                    'logoutUrl'
                ]
            ]);
    }
}
