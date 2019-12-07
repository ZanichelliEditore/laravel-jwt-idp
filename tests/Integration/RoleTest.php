<?php

namespace Tests\Integration;

use Tests\TestCase;

class RoleTest extends TestCase
{
    /**
     * Get all roles by unauthorized user.
     * @test
     * @return void
     */
    public function roleTest()
    {
        $response = $this->json('GET', '/v1/roles');
        $response->assertStatus(401);
    }
}
