<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Services\Mailer;
use Tests\Utility\UserUtility;
use App\Repositories\UserRepository;
use App\Http\Controllers\Manage\UserController;
use App\Repositories\VerificationTokenRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserWithoutMiddlewareTest extends TestCase
{
    use WithoutMiddleware;

    public static function getUserJson()
    {
        return [
            'id',
            'email',
            'is_verified',
            'name',
            'surname',
            'roles',
        ];
    }

    /**
     * @test
     * @return void
     */
    public function getUserTest()
    {
        $user = factory(User::class)->make([
            'id' => 10,
            'is_verified' => true
        ]);
        $mock = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => $user])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mock);

        $response = $this->json('GET', '/v1/users/' . $user->id);
        $response->assertStatus(200)
            ->assertJsonStructure(['user' => $this->getUserJson()]);
    }


    /**
     * @test
     * @return void
     */
    public function getUnknownUserTest()
    {
        $mock = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['find' => null])
            ->withAnyArgs()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mock);

        $userController = new UserController($mock, new VerificationTokenRepository(), new Mailer());
        $response = $userController->find(mt_getrandmax());
        $this->assertEquals($response->status(), 404);
    }


    /**
     * Fail created user.
     * @test
     * @return void
     */
    public function createUserFailTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $mock = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['create' => null])
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mock);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $username = 'myUsername' . Str::random(15);

        $body = [
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'user' . $username . '@example.it',
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(500);
    }


    /**
     * @test
     * @return void
     */
    public function getUsersTest()
    {
        $users = factory(User::class, 2)->make();
        $users[0]['id'] = 1265;
        $users[1]['id'] = 1266;
        $mock = Mockery::mock(UserRepository::class)->makePartial()
            ->shouldReceive(['all' => [
                'data' => $users
            ]])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('App\Repositories\UserRepository', $mock);

        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        $response = $this->json('GET', '/admin/users', [], $cookie);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'email',
                        'surname',
                        'is_verified',
                    ]
                ]
            ]);
    }
}
