<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Utility\UserUtility;

class UserTest extends TestCase
{
    /**
     * Tear down fixture removing example user
     *
     * @return void
     */
    protected function tearDown(): void
    {
        User::where('email', 'prova@example.com')->delete();
        parent::tearDown();
    }

    /**
     * Create user without token.
     * @test
     * @return void
     */
    public function createUserTokenAbsentTest()
    {
        $response = $this->json('POST', '/admin/users');
        $response->assertStatus(302)->assertRedirect('/loginForm');
    }

    /**
     * Validation errors on create user.
     * @test
     * @return void
     */
    public function createUserValidationErrorTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $response = $this->json('POST', '/admin/users', [], $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'name'
            ]
        ]);

        $body = ['username' => 'prova'];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);

        $body = [
            'username' => Str::random(51),
            'name' => Str::random(51),
            'surname' => Str::random(51),
            'email' => Str::random(256)
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'name',
                'surname',
                'email',
            ]
        ]);

        $body = [
            'username' => 123,
            'name' => 123,
            'surname' => 123,
            'email' => 'NotValidEmail'
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'name',
                'surname',
                'email'
            ]
        ]);

        $body = [
            'username' => 'usertest4444@example.it',
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => null
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ]
        ]);

        $body = [
            'username' => 'user@test.com',
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'user@test.com',
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $body = [
            'username' => 'user@test.com',
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'user@test.com',
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'username',
                'email'
            ]
        ]);
    }

    /**
     * Successfull create user.
     * @test
     * @return void
     */
    public function createUserSuccessTest()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $username = 'myUsername' . Str::random(15);

        $body = [
            'username' => 'user' . $username . '@example.it',
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'user' . $username . '@example.it',
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(200);
        unset($body['password']);
        $this->assertDatabaseHas('users', $body);
    }


    /**
     * Fail creating user with email.
     * @test
     * @return void
     */
    public function createUserwithEmailFailTest()
    {
        //Prepare test fixture user with email prova@example.com
        User::create([
            'username' => 'myUsername' . Str::random(15),
            'email' => 'prova@example.com',
            'name' => 'myName2',
            'surname' => 'mySurname2',
            'is_verified' => true
        ]);

        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        $username = 'myUsername' . Str::random(15);
        $body = [
            'username' => $username,
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'prova@example.com',
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ]
        ]);;
    }

    /**
     * Unauthrized available username.
     * @test
     * @return void
     */
    public function availableUsernameTest()
    {
        $response = $this->json('GET', '/v1/user/available/prova');
        $response->assertStatus(401);
    }

    /**
     * Unauthorized find user.
     * @test
     * @return void
     */
    public function findUserTest()
    {
        $response = $this->json('GET', '/v1/users/1');
        $response->assertStatus(401);
    }

    /**
     * Get all users
     * @test
     * @return void
     */
    public function getAllUsersTest()
    {
        $user = UserUtility::getAdmin();
        $n_users = User::count();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        $response = $this->json('GET', '/admin/users', [], $cookie);
        $total = json_decode($response->getContent())->total;
        $response->assertStatus(200);
        $this->assertEquals($n_users, $total);
    }

    /**
     * Get user by email.
     * @test
     * @return void
     */
    public function getFilteredUsersTest()
    {
        $users = factory(User::class, 2)->create();
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->username,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];


        $response = $this->json('GET', '/admin/users?q=' . $users[0]['email'], [], $cookie);
        $data = json_decode($response->getContent())->data;
        $response->assertStatus(200);
        $this->assertCount(1, $data);
    }
}
