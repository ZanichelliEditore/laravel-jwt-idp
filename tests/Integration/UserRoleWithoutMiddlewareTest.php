<?php

namespace Tests\Integration;

use Mockery;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserRoleWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @test
     * @return void
     */
    public function createUserRoleValidationErrorTest()
    {
        $user = factory(User::class)->create();
        $userId = $user->id;
        $user->delete();
        $response = $this->json('POST', '/v1/users/' . $userId . '/user-roles', ['role_id' => 1]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['user_id']
            ]);

        $user = factory(User::class)->create([
            'is_verified' => true
        ]);


        $response = $this->json('POST', '/v1/users/' . $user->id . '/user-roles', ['role_id' => "kdufyow"]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['0.role_id']
            ]);

        $role = factory(Role::class)->create();
        $roleId = $role->id;
        $role->delete();
        $response = $this->json('POST', '/v1/users/' . $user->id . '/user-roles', ['role_id' => $roleId]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['0.role_id']
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function createUserRoleAlreadyExistsTest()
    {

        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive(['where' => collect(["somethink"])])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $mockUser = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => true])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mockUser);

        $response = $this->json('POST', '/v1/users/1/user-roles', ['role_id' => 1]);
        $response->assertStatus(204);
    }

    /**
     * @test
     * @return void
     */
    public function createUserRoleFailCreationTest()
    {

        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive([
                'where' => collect([]),
                'create' => false
            ])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $mockUser = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => true])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mockUser);

        $response = $this->json('POST', '/v1/users/1/user-roles', ['role_id' => 1]);
        $response->assertStatus(503)
            ->assertJsonStructure([
                'message'
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function createUserRoleSuccessTest()
    {

        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive([
                'where' => collect([]),
                'create' => true
            ])
            ->getMock();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $mockUser = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => true])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mockUser);

        $response = $this->json('POST', '/v1/users/1/user-roles', [
            ['role_id' => 1],
            ['role_id' => 1]
        ]);
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    [
                        'role_id' => 1,
                        'user_id' => 1
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function createUserRoleDeleteFirstInsertedTest()
    {
        $role = factory(Role::class)->create();
        $userRole = factory(UserRole::class)->create();
        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive(['where' => collect([])])
            ->getMock();
        $mockUserRole->shouldReceive('create')
            ->with(['role_id' => $role->id, 'user_id' => 1])
            ->andReturn($userRole)
            ->once();
        $mockUserRole->shouldReceive('create')
            ->with(['role_id' => $role->id, 'user_id' => 1])
            ->andReturnFalse()
            ->once();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $mockUser = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => true])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mockUser);

        $response = $this->json('POST', '/v1/users/1/user-roles', [
            ['role_id' => $role->id],
            ['role_id' => $role->id]
        ]);
        $response->assertStatus(503)
            ->assertJson([
                'message' => "Error on save, retry"
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function deleteUserRoleNotFoundTest()
    {
        $userRole = factory(UserRole::class)->create();
        $userRoleId = $userRole->id;
        $userRole->delete();
        $response = $this->json('DELETE', '/v1/user-role/' . $userRoleId);
        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function deleteUserRoleErrorTest()
    {
        $userRole = factory(UserRole::class)->create();
        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive(['delete' => false, 'find' => $userRole])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $response = $this->json('DELETE', '/v1/user-role/1');
        $response->assertStatus(500)
            ->assertJsonStructure([
                'message',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function deleteUserRoleMockTest()
    {
        $userRole = factory(UserRole::class)->create();
        $mockUserRole = Mockery::mock(UserRoleRepository::class)->makePartial()
            ->shouldReceive(['delete' => true, 'find' => $userRole])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRoleRepository', $mockUserRole);

        $response = $this->json('DELETE', '/v1/user-role/1');
        $response->assertStatus(204);
    }

    /**
     * @test
     * @return void
     */
    public function deleteUserRoleTest()
    {
        $userRole = factory(UserRole::class)->create();
        $response = $this->json('DELETE', '/v1/user-role/' . $userRole->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('user_roles', $userRole->toArray());
    }

    /**
     * @test
     * @return void
     */
    public function getUserRoleTest()
    {
        $userRole = factory(UserRole::class)->create();
        $response = $this->json('GET', '/v1/users/' . $userRole->user->id . '/user-roles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'roleName',
                        'roleId',
                        'id'
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function getUserRoleByRoleTest()
    {
        $userRole = factory(UserRole::class)->create();
        $response = $this->json('GET', '/v1/roles/' . $userRole->role->id . '/user-roles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'user_id',
                        'role_id',
                        'id'
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function GetUserRoleNotFoundUserMockTest()
    {
        $user = factory(User::class)->create();

        $mockUser = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => null])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mockUser);

        $response = $this->json('GET', '/v1/users/' . $user->id . '/user-roles');
        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    /**
     * @test
     * @return void
     */
    public function GetUserRoleByRoleNotFoundUserMockTest()
    {
        $role = factory(Role::class)->create();

        $mockRole = Mockery::mock(RoleRepository::class)->makePartial()
            ->shouldReceive(['find' => null])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\RoleRepository', $mockRole);

        $response = $this->json('GET', '/v1/roles/' . $role->id . '/user-roles');
        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}
