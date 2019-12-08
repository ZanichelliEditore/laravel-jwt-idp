<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\VerificationToken;

class VerificationTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function failValidationTest()
    {

        // Invalid parameters
        $data = ['token' => '', 'password' => ''];
        $response = $this->json('POST', '/v1/complete-registration', $data);
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid parameters.'
            ]);


        $user = User::create([
            'email' => Str::random(30) . '@example.com',
            'name' => 'myName2',
            'surname' => 'mySurname2',
            'password' => Str::random(30),
            'is_verified' => true
        ]);

        // Invalid parameters for empty password
        $token2 = VerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(50)
        ]);

        $data = ['token' => $token2->token, 'password' => '     '];
        $response = $this->json('POST', '/v1/complete-registration', $data);
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid parameters.'
            ]);

        // Invalid Token with empty field
        $data = ['token' => 'a', 'password' => 'testpassword'];
        $response = $this->json('POST', '/v1/complete-registration', $data);
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid token'
            ]);


        // Invalid Token with token set not valid
        $token = VerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(50),
            'is_valid' => 0
        ]);

        $data = ['token' => $token->token, 'password' => 'testpassword'];
        $response = $this->json('POST', '/v1/complete-registration', $data);
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid token'
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function validationTokenTest()
    {
        $user = User::create([
            'email' => Str::random(30) . '@example.com',
            'name' => 'myName2',
            'password' => Str::random(30),
            'surname' => 'mySurname2',
            'is_verified' => true
        ]);


        $token = VerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(50)
        ]);

        $data = ['token' => $token->token, 'password' => 'testpassword'];
        $response = $this->json('POST', '/v1/complete-registration', $data);
        $response->assertStatus(204);
    }
}
