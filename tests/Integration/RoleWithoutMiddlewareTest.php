<?php

namespace Tests\Integration;

use App\Repositories\RoleRepository;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Role;

class RoleWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Get all roles.
     * @test
     * @return void
     */
    public function retrieveRolesTest()
    {
        factory(Role::class)->create();
        $response = $this->json('GET', '/v1/roles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'name'
                ]
            ]);
    }

    /**
     * Create a new role.
     * @test
     * @return void
     */
    public function createRoleTest()
    {
        $role = factory(Role::class)->make();
        $role->id = 1;

        $repositoryMock = Mockery::mock(RoleRepository::class)->makePartial()
            ->shouldReceive(['create'=> collect(['name' => $role->name])])
            ->once()
            ->andReturn($role)
            ->getMock();

        $this->app->instance('App\Repositories\RoleRepository', $repositoryMock);

        $response = $this->json('POST', '/v1/roles', ['name' => $role->name]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name'
            ]);
    }

    /**
     * Existing role test.
     * @test
     * @return void
     */
    public function existingRoleTest()
    {
        $role = factory(Role::class)->create();

        $response = $this->json('POST', '/v1/roles', ['name' => $role->name]);
        $response->assertStatus(422);

        $role->delete();
    }

    /**
     * Existing role test.
     * @test
     * @return void
     */
    public function deletingInexistentRole()
    {
        $response = $this->json('DELETE', '/v1/roles/99999999999999');
        $response->assertStatus(404);
    }

    /**
     * Delete role test.
     * @test
     * @return void
     */
    public function deletingRole()
    {
        $role = factory(Role::class)->create();

        $response = $this->json('DELETE', '/v1/roles/' . $role->id);
        $response->assertStatus(204);
    }

    /**
     * Delete role test.
     * @test
     * @return void
     */
    public function errorOnDeletingRole()
    {
        $role = factory(Role::class)->create();

        $repositoryMock = Mockery::mock(RoleRepository::class)->makePartial()
            ->shouldReceive(['delete' => false])
            ->once()
            ->getMock();

        $this->app->instance('App\Repositories\RoleRepository', $repositoryMock);

        $response = $this->json('DELETE', '/v1/roles/' . $role->id);
        $response->assertStatus(500);
    }
}
