<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\OauthClient;
use Tests\Utility\UserUtility;
use Illuminate\Pagination\Factory;
use App\Repositories\ClientRoleRepository;


class OauthClientTest extends TestCase
{  
    /**
     * Get oauth_clients
     *
     * @return void
     */

    public function testAllOauthUsers()
    {
        $user = UserUtility::getAdmin();
        $n_oauth_clients = OauthClient::count();
        
        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        $response = $this->json('GET', '/admin/oauth-clients-all', [], $cookie);
        $response->assertStatus(200);
        $response =  $response->decodeResponseJson();
        $total = $response['meta']['total'];
        $this->assertEquals($n_oauth_clients, $total);
    }

    /**
     * Get oauth_clients filtered by params
     *
     * @return void
     */

    public function testAllOauthUsersWithParameters()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];

        
        $response = $this->json('GET', '/admin/oauth-clients-all?q=admin manager', [], $cookie);
        $response->assertStatus(200);
    }

    /**
     * Update oauth_clients roles
     *
     * @return void
     */

    public function testUpdateRoles()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $clientRoleRepository = new ClientRoleRepository;
        $roles = $clientRoleRepository->all();

        $body = [
            "clientId" => 1,
            "roles" =>  $roles
        ];
        
        $response = $this->json('PUT', '/admin/update-roles', $body, $cookie);
        $response->assertStatus(204);
    }

    public function testUpdateRolesIfOauthClientDontExist()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $clientRoleRepository = new ClientRoleRepository;
        $roles = $clientRoleRepository->all();

        $body = [
            "clientId" => 432,
            "roles" =>  $roles
        ];
        
        $response = $this->json('PUT', '/admin/update-roles', $body, $cookie);
        $response->assertStatus(500);
    }

    public function testUpdateRolesIfClientDontExist()
    {
        $user = UserUtility::getAdmin();

        $response = $this->json('POST', 'v2/login', [
            'username' => $user->email,
            'password' => 'secret'
        ]);

        $cookie = ['token' => json_decode($response->getContent())->token];
        $clientRoleRepository = new ClientRoleRepository;
        $roles = $clientRoleRepository->all();

        $body = [
            "clientId" => 15,
            "roles" =>  $roles
        ];
        
        $response = $this->json('PUT', '/admin/update-roles', $body, $cookie);
        $response->assertStatus(500);

        $oauthClient = factory(OauthClient::class)->create();
        $body = [
            "clientId" => $oauthClient->id,
            "roles" =>  $roles
        ];

        $response = $this->json('PUT', '/admin/update-roles', $body, $cookie);
        $response->assertStatus(204);
        
    }
}
