<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CheckClientRoleTest extends TestCase
{

    const GRANT_TYPE = 'client_credentials';
    const HEADER_AUTH_NAME = 'HTTP_Authorization';
    const HEADER_AUTH_TYPE = 'Bearer';
    const BASIC_HEADER_AUTH_NAME = 'Authorization';
    const BASIC_HEADER_AUTH_TYPE = 'Basic';
    const HEADER_ACCEPT = 'Accept';
    const HEADER_ACCEPTED_TYPE = 'application/json';
    const MANAGE_IDP = 'manage-idp';
    const MANAGE_USER = 'manage-user';
    const FORBIDDEN_MESSAGE = [
        'message' => 'You are not authorized to use this resource'
    ];

    const MANAGER_EXAMPLE = [
        "id" => 1,
        "secret" => 'HkZ5sCBaAKRH0B5CIlBGjNIQazfYDxi4EDth3ANa'
    ];

    const CLIENT_EXAMPLE = [
        "id" => 3,
        "secret" => 'mydhRDjLRMNuubmmHfs8u2DURLEc91qoc6fS58kT'
    ];

    const ADMIN_EXAMPLE = [
        'id' => 2,
        'secret' => '6ZWpCgKPYc93TbgKHKnZMiULFStw88lIvquDQETQ'
    ];

    /**
     * A setup method launched at the beginning of test
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');
    }

    /**
     * A setup method launched at the end of test
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Get oauth token
     * @return  string
     */
    public function getToken($scopes, $clientId, $clientSecret)
    {
        $bodyParams = [
            "client_id" => $clientId,
            "client_secret" => $clientSecret,
            "grant_type" => self::GRANT_TYPE,
            "scope" => $scopes
        ];

        try {
            $content = $this->call('POST', env('APP_URL') . '/oauth/token', $bodyParams)->getContent();
            $body = json_decode($content);
            $access_token = $body->access_token;
        } catch (Exception $e) {
            Log::error('Test error: ' . (string) $e);
            $access_token = '';
        }
        return $access_token;
    }

    /**
     * @test
     * @return void
     */
    public function testAdminRoleOnProtectedRouteSuccess()
    {
        $scopes = self::MANAGE_USER . ' ' . self::MANAGE_IDP;
        $header = [
            self::HEADER_ACCEPT => self::HEADER_ACCEPTED_TYPE,
            self::HEADER_AUTH_NAME => self::HEADER_AUTH_TYPE . ' ' . $this->getToken($scopes, self::ADMIN_EXAMPLE['id'], self::ADMIN_EXAMPLE['secret'])
        ];

        $response = $this->call('GET', env('APP_URL') . '/v1/roles', [], [], [], $header);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode([
            ["id" => 2, "name" => "ADMIN"],
            ["id" => 1, "name" => "USER"]
        ]), $response->getContent());
    }

    /**
     * @test
     * @return void
     */
    public function testMangerRoleOnProtectedRouteSuccess()
    {
        $scopes = self::MANAGE_USER . ' ' . self::MANAGE_IDP;
        $header = [
            self::HEADER_ACCEPT => self::HEADER_ACCEPTED_TYPE,
            self::HEADER_AUTH_NAME => self::HEADER_AUTH_TYPE . ' ' . $this->getToken($scopes, self::MANAGER_EXAMPLE['id'], self::MANAGER_EXAMPLE['secret'])
        ];

        $response = $this->call('GET', env('APP_URL') . '/v1/roles', [], [], [], $header);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode([
            ["id" => 2, "name" => "ADMIN"],
            ["id" => 1, "name" => "USER"]
        ]), $response->getContent());
    }

    /**
     * @test
     * @return void
     */
    public function testMangerRoleOnProtectedRouteFail()
    {
        $scopes = self::MANAGE_USER . ' ' . self::MANAGE_IDP;
        $header = [
            self::HEADER_ACCEPT => self::HEADER_ACCEPTED_TYPE,
            self::HEADER_AUTH_NAME => self::HEADER_AUTH_TYPE . ' ' . $this->getToken($scopes, self::MANAGER_EXAMPLE['id'], self::MANAGER_EXAMPLE['secret'])
        ];

        $response = $this->call('POST', env('APP_URL') . '/v1/roles', [], [], [], $header);
        $this->assertEquals(403, $response->status());
        $this->assertEquals(json_encode(self::FORBIDDEN_MESSAGE), $response->getContent());
    }

    /**
     * @test
     * @return void
     */
    public function testUserRoleOnProtectedRouteFail()
    {
        $scopes = self::MANAGE_USER . ' ' . self::MANAGE_IDP;
        $header = [
            self::HEADER_ACCEPT => self::HEADER_ACCEPTED_TYPE,
            self::HEADER_AUTH_NAME => self::HEADER_AUTH_TYPE . ' ' . $this->getToken($scopes, self::CLIENT_EXAMPLE['id'], self::CLIENT_EXAMPLE['secret'])
        ];

        $response = $this->call('POST', env('APP_URL') . '/v1/roles', ['name' => 'TEST_ROLE'], [], [], $header);
        $this->assertEquals(403, $response->status());
        $this->assertEquals(json_encode(self::FORBIDDEN_MESSAGE), $response->getContent());
    }
}
