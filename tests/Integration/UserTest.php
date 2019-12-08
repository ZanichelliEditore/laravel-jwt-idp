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
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $response = $this->json('POST', '/admin/users', [], $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'email'
            ]
        ]);

        $body = [
            'name' => Str::random(51),
            'surname' => Str::random(51),
            'email' => Str::random(256)
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'surname',
                'email',
            ]
        ]);

        $body = [
            'name' => 123,
            'surname' => 123,
            'email' => 'NotValidEmail'
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'surname',
                'email'
            ]
        ]);

        $body = [
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
            'name' => 'myName',
            'surname' => 'mySurname',
            'email' => 'user@test.com'
        ];
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(200);
        $response = $this->json('POST', '/admin/users', $body, $cookie);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
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
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $username = 'myUsername' . Str::random(15);

        $body = [
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
            'email' => 'prova@example.com',
            'name' => 'myName2',
            'surname' => 'mySurname2',
            'is_verified' => true
        ]);

        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        $body = [
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
            'username' => $user->email,
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
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];


        $response = $this->json('GET', '/admin/users?q=' . $users[0]['email'], [], $cookie);
        $data = json_decode($response->getContent())->data;
        $response->assertStatus(200);
        $this->assertCount(1, $data);
    }
}
